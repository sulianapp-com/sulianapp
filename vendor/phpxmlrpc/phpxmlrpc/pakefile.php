<?php
/**
 * Makefile for phpxmlrpc library.
 * To be used with the Pake tool: https://github.com/indeyets/pake/wiki
 *
 * @copyright (c) 2015 G. Giunta
 *
 * @todo !important allow user to specify location of docbook xslt instead of the one installed via composer
 */

namespace PhpXmlRpc {

class Builder
{
    protected static $buildDir = 'build';
    protected static $libVersion;
    protected static $tools = array(
        'asciidoctor' => 'asciidoctor',
        'fop' => 'fop',
        'php' => 'php',
        'zip' => 'zip',
    );
    protected static $options = array(
        'repo' => 'https://github.com/gggeek/phpxmlrpc',
        'branch' => 'php53'
    );

    public static function libVersion()
    {
        if (self::$libVersion == null)
            throw new \Exception('Missing library version argument');
        return self::$libVersion;
    }

    public static function buildDir()
    {
        return self::$buildDir;
    }

    public static function workspaceDir()
    {
        return self::buildDir().'/workspace';
    }

    /// most likely things will break if this one is moved outside of BuildDir
    public static function distDir()
    {
        return self::buildDir().'/xmlrpc-'.self::libVersion();
    }

    /// these will be generated in BuildDir
    public static function distFiles()
    {
        return array(
            'xmlrpc-'.self::libVersion().'.tar.gz',
            'xmlrpc-'.self::libVersion().'.zip',
        );
    }

    public static function getOpts($args=array(), $cliOpts=array())
    {
        if (count($args) > 0)
        //    throw new \Exception('Missing library version argument');
            self::$libVersion = $args[0];

        foreach (self::$tools as $name => $binary) {
            if (isset($cliOpts[$name])) {
                self::$tools[$name] = $cliOpts[$name];
            }
        }

        foreach (self::$options as $name => $value) {
            if (isset($cliOpts[$name])) {
                self::$options[$name] = $cliOpts[$name];
            }
        }

        //pake_echo('---'.self::$libVersion.'---');
    }

    /**
     * @param string $name
     * @return string
     */
    public static function tool($name)
    {
        return self::$tools[$name];
    }

    /**
     * @param string $name
     * @return string
     */
    public static function option($name)
    {
        return self::$options[$name];
    }

    /**
     * @param string $inFile
     * @param string $xssFile
     * @param string $outFileOrDir
     * @throws \Exception
     */
    public static function applyXslt($inFile, $xssFile, $outFileOrDir)
    {

        if (!file_exists($inFile)) {
            throw new \Exception("File $inFile cannot be found");
        }
        if (!file_exists($xssFile)) {
            throw new \Exception("File $xssFile cannot be found");
        }

        // Load the XML source
        $xml = new \DOMDocument();
        $xml->load($inFile);
        $xsl = new \DOMDocument();
        $xsl->load($xssFile);

        // Configure the transformer
        $processor = new \XSLTProcessor();
        if (version_compare(PHP_VERSION, '5.4', "<")) {
            if (defined('XSL_SECPREF_WRITE_FILE')) {
                ini_set("xsl.security_prefs", XSL_SECPREF_CREATE_DIRECTORY | XSL_SECPREF_WRITE_FILE);
            }
        } else {
            // the php online docs only mention setSecurityPrefs, but somehow some installs have setSecurityPreferences...
            if (method_exists('XSLTProcessor', 'setSecurityPrefs')) {
                $processor->setSecurityPrefs(XSL_SECPREF_CREATE_DIRECTORY | XSL_SECPREF_WRITE_FILE);
            } else {
                $processor->setSecurityPreferences(XSL_SECPREF_CREATE_DIRECTORY | XSL_SECPREF_WRITE_FILE);
            }
        }
        $processor->importStyleSheet($xsl); // attach the xsl rules

        if (is_dir($outFileOrDir)) {
            if (!$processor->setParameter('', 'base.dir', realpath($outFileOrDir))) {
                echo "setting param base.dir KO\n";
            }
        }

        $out = $processor->transformToXML($xml);

        if (!is_dir($outFileOrDir)) {
            file_put_contents($outFileOrDir, $out);
        }
    }

    public static function highlightPhpInHtml($content)
    {
        $startTag = '<pre class="programlisting">';
        $endTag = '</pre>';

        //$content = file_get_contents($inFile);
        $last = 0;
        $out = '';
        while (($start = strpos($content, $startTag, $last)) !== false) {
            $end = strpos($content, $endTag, $start);
            $code = substr($content, $start + strlen($startTag), $end - $start - strlen($startTag));
            if ($code[strlen($code) - 1] == "\n") {
                $code = substr($code, 0, -1);
            }

            $code = str_replace(array('&gt;', '&lt;'), array('>', '<'), $code);
            $code = highlight_string('<?php ' . $code, true);
            $code = str_replace('<span style="color: #0000BB">&lt;?php&nbsp;<br />', '<span style="color: #0000BB">', $code);

            $out = $out . substr($content, $last, $start + strlen($startTag) - $last) . $code . $endTag;
            $last = $end + strlen($endTag);
        }
        $out .= substr($content, $last, strlen($content));

        return $out;
    }
}

}

namespace {

use PhpXmlRpc\Builder;

function run_default($task=null, $args=array(), $cliOpts=array())
{
    echo "Syntax: pake {\$pake-options} \$task \$lib-version [\$git-tag] {\$task-options}\n";
    echo "\n";
    echo "  Run 'pake help' to list all pake options\n";
    echo "  Run 'pake -T' to list available tasks\n";
    echo "  Run 'pake -P' to list all available tasks (including hidden ones) and their dependencies\n";
    echo "\n";
    echo "  Task options:\n";
    echo "      --repo=REPO      URL of the source repository to clone. Defaults to the github repo.\n";
    echo "      --branch=BRANCH  The git branch to build from.\n";
    echo "      --asciidoctor=ASCIIDOCTOR Location of the asciidoctor command-line tool\n";
    echo "      --fop=FOP        Location of the apache fop command-line tool\n";
    echo "      --php=PHP        Location of the php command-line interpreter\n";
    echo "      --zip=ZIP        Location of the zip tool\n";
}

function run_getopts($task=null, $args=array(), $cliOpts=array())
{
    Builder::getOpts($args, $cliOpts);
}

/**
 * Downloads source code in the build workspace directory, optionally checking out the given branch/tag
 */
function run_init($task=null, $args=array(), $cliOpts=array())
{
    // download the current version into the workspace
    $targetDir = Builder::workspaceDir();

    // check if workspace exists and is not already set to the correct repo
    if (is_dir($targetDir) && pakeGit::isRepository($targetDir)) {
        $repo = new pakeGit($targetDir);
        $remotes = $repo->remotes();
        if (trim($remotes['origin']['fetch']) != Builder::option('repo')) {
            throw new Exception("Directory '$targetDir' exists and is not linked to correct git repo");
        }

        /// @todo should we not just fetch instead?
        $repo->pull();
    } else {
        pake_mkdirs(dirname($targetDir));
        $repo = pakeGit::clone_repository(Builder::option('repo'), Builder::workspaceDir());
    }

    $repo->checkout(Builder::option('branch'));
}

/**
 * Runs all the build steps.
 *
 * (does nothing by itself, as all the steps are managed via task dependencies)
 */
function run_build($task=null, $args=array(), $cliOpts=array())
{
}

function run_clean_doc()
{
    pake_remove_dir(Builder::workspaceDir().'/doc/api');
    $finder = pakeFinder::type('file')->name('*.html');
    pake_remove($finder, Builder::workspaceDir().'/doc/manual');
    $finder = pakeFinder::type('file')->name('*.xml');
    pake_remove($finder, Builder::workspaceDir().'/doc/manual');
}

/**
 * Generates documentation in all formats
 */
function run_doc($task=null, $args=array(), $cliOpts=array())
{
    $docDir = Builder::workspaceDir().'/doc';

    // API docs

    // from phpdoc comments using phpdocumentor
    $cmd = Builder::tool('php');
    pake_sh("$cmd vendor/phpdocumentor/phpdocumentor/bin/phpdoc run -d ".Builder::workspaceDir().'/src'." -t ".Builder::workspaceDir().'/doc/api --title PHP-XMLRPC');

    // User Manual

    // html (single file) from asciidoc
    $cmd = Builder::tool('asciidoctor');
    pake_sh("$cmd -d book $docDir/manual/phpxmlrpc_manual.adoc");

    // then docbook from asciidoc
    /// @todo create phpxmlrpc_manual.xml with the good version number
    /// @todo create phpxmlrpc_manual.xml with the date set to the one of last commit (or today?)
    pake_sh("$cmd -d book  -b docbook $docDir/manual/phpxmlrpc_manual.adoc");

    # Other tools for docbook...
    #
    # jade cmd yet to be rebuilt, starting from xml file and putting output in ./out dir, e.g.
    #	jade -t xml -d custom.dsl xmlrpc_php.xml
    #
    # convertdoc command for xmlmind xxe editor
    #	convertdoc docb.toHTML xmlrpc_php.xml -u out
    #
    # saxon + xerces xml parser + saxon extensions + xslthl: adds a little syntax highligting
    # (bold and italics only, no color) for php source examples...
    #	java \
    #	-classpath c:\programmi\saxon\saxon.jar\;c:\programmi\saxon\xslthl.jar\;c:\programmi\xerces\xercesImpl.jar\;C:\htdocs\xmlrpc_cvs\docbook-xsl\extensions\saxon65.jar \
    #	-Djavax.xml.parsers.DocumentBuilderFactory=org.apache.xerces.jaxp.DocumentBuilderFactoryImpl \
    #	-Djavax.xml.parsers.SAXParserFactory=org.apache.xerces.jaxp.SAXParserFactoryImpl \
    #	-Dxslthl.config=file:///c:/htdocs/xmlrpc_cvs/docbook-xsl/highlighting/xslthl-config.xml \
    #	com.icl.saxon.StyleSheet -o xmlrpc_php.fo.xml xmlrpc_php.xml custom.fo.xsl use.extensions=1

    // HTML (multiple files) from docbook - discontinued, as we use the nicer-looking html gotten from asciidoc
    /*Builder::applyXslt($docDir.'/manual/phpxmlrpc_manual.xml', $docDir.'/build/custom.xsl', $docDir.'/manual');
    // post process html files to highlight php code samples
    foreach(pakeFinder::type('file')->name('*.html')->in($docDir.'/manual') as $file)
    {
        file_put_contents($file, Builder::highlightPhpInHtml(file_get_contents($file)));
    }*/

    // PDF file from docbook

    // convert to fo and then to pdf using apache fop
    Builder::applyXslt($docDir.'/manual/phpxmlrpc_manual.xml', $docDir.'/build/custom.fo.xsl', $docDir.'/manual/phpxmlrpc_manual.fo.xml');
    $cmd = Builder::tool('fop');
    pake_sh("$cmd $docDir/manual/phpxmlrpc_manual.fo.xml $docDir/manual/phpxmlrpc_manual.pdf");

    // cleanup
    unlink($docDir.'/manual/phpxmlrpc_manual.xml');
    unlink($docDir.'/manual/phpxmlrpc_manual.fo.xml');
}

function run_clean_dist()
{
    pake_remove_dir(Builder::distDir());
    $finder = pakeFinder::type('file')->name(Builder::distFiles());
    pake_remove($finder, Builder::buildDir());
}

/**
 * Creates the tarballs for a release
 */
function run_dist($task=null, $args=array(), $cliOpts=array())
{
    // copy workspace dir into dist dir, without git
    pake_mkdirs(Builder::distDir());
    $finder = pakeFinder::type('any')->ignore_version_control();
    pake_mirror($finder, realpath(Builder::workspaceDir()), realpath(Builder::distDir()));

    // remove unwanted files from dist dir

    // also: do we still need to run dos2unix?

    // create tarballs
    $cwd = getcwd();
    chdir(dirname(Builder::distDir()));
    foreach(Builder::distFiles() as $distFile) {
        // php can not really create good zip files via phar: they are not compressed!
        if (substr($distFile, -4) == '.zip') {
            $cmd = Builder::tool('zip');
            $extra = '-9 -r';
            pake_sh("$cmd $distFile $extra ".basename(Builder::distDir()));
        }
        else {
            $finder = pakeFinder::type('any')->pattern(basename(Builder::distDir()).'/**');
            // see https://bugs.php.net/bug.php?id=58852
            $pharFile = str_replace(Builder::libVersion(), '_LIBVERSION_', $distFile);
            pakeArchive::createArchive($finder, '.', $pharFile);
            rename($pharFile, $distFile);
        }
    }
    chdir($cwd);
}

function run_clean_workspace($task=null, $args=array(), $cliOpts=array())
{
    pake_remove_dir(Builder::workspaceDir());
}

/**
 * Cleans up the whole build directory
 * @todo 'make clean' usually just removes the results of the build, distclean removes all but sources
 */
function run_clean($task=null, $args=array(), $cliOpts=array())
{
    pake_remove_dir(Builder::buildDir());
}

// helper task: display help text
pake_task( 'default' );
// internal task: parse cli options
pake_task('getopts');
pake_task('init', 'getopts');
pake_task('doc', 'getopts', 'init', 'clean-doc');
pake_task('build', 'getopts', 'init', 'doc');
pake_task('dist', 'getopts', 'init', 'build', 'clean-dist');
pake_task('clean-doc', 'getopts');
pake_task('clean-dist', 'getopts');
pake_task('clean-workspace', 'getopts');
pake_task('clean', 'getopts');

}
