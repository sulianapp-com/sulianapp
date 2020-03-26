<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 18/04/2017
 * Time: 09:12
 */

namespace app\common\services;


use app\common\models\Setting;
use Illuminate\Filesystem\Filesystem;
use Ixudra\Curl\Facades\Curl;
use \vierbergenlars\SemVer\version;
use \vierbergenlars\SemVer\expression;
use \vierbergenlars\SemVer\SemVerException;
/**
 * Auto update class.
 *
 * update.json
 * {
"0.1.0": "http://domain/server/0.1.0.zip",
"0.2.0": "http://domain/server/0.2.0.zip",
"0.2.1": "http://domain/server/0.2.1.zip"
}
 */
class AutoUpdate
{
    /**
     * The latest version.
     *
     * @var \vierbergenlars\SemVer\version
     */
    private $_latestVersion = null;
    /**
     * Updates not yet installed.
     *
     * @var array
     */
    private $_updates = null;
    /**
     * Cache for update requests.
     *
     */
    private $_cache = null;
    /**
     * Result of simulated install.
     *
     * @var array
     */
    private $_simulationResults = array();
    /**
     * Temporary download directory.
     *
     * @var string
     */
    private $_tempDir = '';
    /**
     * Install directory.
     *
     * @var string
     */
    private $_installDir = '';
    /**
     * Update branch.
     *
     * @var string
     */
    private $_branch = '';
    /**
     * Url to the update folder on the server.
     *
     * @var string
     */
    protected $_updateUrl = 'https://example.com/updates/';
    /**
     * Version filename on the server.
     *
     * @var string
     */
    protected $_updateFile = 'check.json';
    /**
     * Current version.
     *
     * @var \vierbergenlars\SemVer\version
     */
    protected $_currentVersion = null;
    /**
     * Create new folders with this privileges.
     *
     * @var int
     */
    public $dirPermissions = 0755;
    /**
     * Update script filename.
     *
     * @var string
     */
    public $updateScriptName = 'update.php';
    /**
     * Username authentication
     *
     * @var string
     */
    private $_username = '';
    /**
     * Password authentication
     *
     * @var string
     */
    private $_password = '';
    /*
     * Callbacks to be called when each update is finished
     */
    private $onEachUpdateFinishCallbacks = [];
    /*
     * Callbacks to be called when all updates are finished
     */
    private $onAllUpdateFinishCallbacks = [];
    /**
     * No update available.
     */
    const NO_UPDATE_AVAILABLE = 0;
    /**
     * Zip file could not be opened.
     */
    const ERROR_INVALID_ZIP = 10;
    /**
     * Could not check for last version.
     */
    const ERROR_VERSION_CHECK = 20;
    /**
     * Temp directory does not exist or is not writable.
     */
    const ERROR_TEMP_DIR = 30;
    /**
     * Install directory does not exist or is not writable.
     */
    const ERROR_INSTALL_DIR = 35;
    /**
     * Could not download update.
     */
    const ERROR_DOWNLOAD_UPDATE = 40;
    /**
     * Could not delete zip update file.
     */
    const ERROR_DELETE_TEMP_UPDATE = 50;
    /**
     * Error while installing the update.
     */
    const ERROR_INSTALL = 60;
    /**
     * Error in simulated install.
     */
    const ERROR_SIMULATE = 70;
    /**
     * Create new instance
     *
     * @param string $tempDir
     * @param string $installDir
     * @param int    $maxExecutionTime
     */
    public function __construct($tempDir = null, $installDir = null, $maxExecutionTime = 0)
    {
        // Init logger
        $this->_log = app('log');
        $this->setTempDir(($tempDir !== null) ? $tempDir : storage_path('app/auto-update/temp'));
        $this->setInstallDir(($installDir !== null) ? $installDir : base_path());
        $this->_latestVersion = new version('0.0.0');
        $this->_currentVersion = new version('0.0.0');
        // Init cache
        $this->_cache = app('cache');
        ini_set('max_execution_time', $maxExecutionTime);
    }
    /**
     * Set the temporary download directory.
     *
     * @param string $dir
     * @return $this|void
     */
    public function setTempDir($dir)
    {
        $dir = $this->addTrailingSlash($dir);
        if (!is_dir($dir)) {
            $this->_log->debug(sprintf('Creating new temporary directory "%s"', $dir));
            if (!mkdir($dir, 0755, true)) {
                $this->_log->critical(sprintf('Could not create temporary directory "%s"', $dir));
                return;
            }
        }
        $this->_tempDir = $dir;
        return $this;
    }
    /**
     * Set the install directory.
     *
     * @param string $dir
     * @return $this|void
     */
    public function setInstallDir($dir)
    {
        $dir = $this->addTrailingSlash($dir);
        if (!is_dir($dir)) {
            $this->_log->debug(sprintf('Creating new install directory "%s"', $dir));
            if (!mkdir($dir, 0755, true)) {
                $this->_log->critical(sprintf('Could not create install directory "%s"', $dir));
                return;
            }
        }
        $this->_installDir = $dir;
        return $this;
    }
    /**
     * Set the update filename.
     *
     * @param string $updateFile
     * @return $this
     */
    public function setUpdateFile($updateFile)
    {
        $this->_updateFile = $updateFile;
        return $this;
    }
    /**
     * Set the update filename.
     *
     * @param string $updateUrl
     * @return $this
     */
    public function setUpdateUrl($updateUrl)
    {
        $this->_updateUrl = $updateUrl;
        return $this;
    }
    /**
     * Set the update branch.
     *
     * @param string branch
     * @return $this
     */
    public function setBranch($branch)
    {
        $this->_branch = $branch;
        return $this;
    }

    /**
     * Set the version of the current installed software.
     *
     * @param string $currentVersion
     *
     * @return bool
     */
    public function setCurrentVersion($currentVersion)
    {
        $version = new version($currentVersion);
        if ($version->valid() === null) {
            $this->_log->error(sprintf('Invalid current version "%s"', $currentVersion));
            return false;
        }
        $this->_currentVersion = $version;
        return $this;
    }
    /**
     * Set authentication
     * @param $username
     * @param $password
     */
    public function setBasicAuth($username, $password)
    {
        $this->_username = $username;
        $this->_password = $password;
    }
    /**
     * Set authentication in update method of users and password exist
     * @return null|resource
     */
    private function _useBasicAuth()
    {
        if ($this->_username && $this->_password) {
            return stream_context_create(array(
                'http' => array(
                    'header' => "Authorization: Basic " . base64_encode("$this->_username:$this->_password")
                )
            ));
        }
        return null;
    }

    /**
     * Get the name of the latest version.
     *
     * @return \vierbergenlars\SemVer\version
     */
    public function getLatestVersion()
    {
        return $this->_latestVersion;
    }

    public function getUpdates()
    {
        return $this->_updates;
    }

    /**
     * Get an array of versions which will be installed.
     *
     * @return array
     */
    public function getVersionsToUpdate()
    {
        return array_map(function ($update) {
            return $update['version'];
        }, $this->_updates);
    }
    /**
     * Get the results of the last simulation.
     *
     * @return array
     */
    public function getSimulationResults()
    {
        return $this->_simulationResults;
    }
    /**
     * Remove directory recursively.
     *
     * @param string $dir
     *
     * @return void
     */
    private function _removeDir($dir)
    {
        $this->_log->debug(sprintf('Remove directory "%s"', $dir));
        if (!is_dir($dir)) {
            $this->_log->warning(sprintf('"%s" is not a directory!', $dir));
            return false;
        }
        $objects = array_diff(scandir($dir), array('.', '..'));
        foreach ($objects as $object) {
            if (is_dir($dir . DIRECTORY_SEPARATOR . $object))
                $this->_removeDir($dir . DIRECTORY_SEPARATOR . $object);
            else
                unlink($dir . DIRECTORY_SEPARATOR . $object);
        }
        return rmdir($dir);
    }
    /**
     * Check for a new version
     *
     * @return int|bool
     *         true: New version is available
     *         false: Error while checking for update
     *         int: Status code (i.e. AutoUpdate::NO_UPDATE_AVAILABLE)
     */
    public function checkUpdate()
    {
        $this->_log->notice('Checking for a new update...');
        // Reset previous updates
        $this->_latestVersion = new version('0.0.0');
        $this->_updates = [];
        $versions = $this->_cache->get('update-versions');
        // Create absolute url to update file
        $updateFile = $this->_updateUrl . '/' . $this->_updateFile;
        if (!empty($this->_branch))
            $updateFile .= '.' . $this->_branch;
        // Check if cache is empty
        if ($versions === null || $versions === false) {
            $this->_log->debug(sprintf('Get new updates from %s', $updateFile));
            // Read update file from update server
            //$update = @file_get_contents($updateFile, $this->_useBasicAuth());

            $data = [
                'domain'  => request()->getHost()
            ];

            $update = Curl::to($updateFile)
                ->withHeader(
                    "Authorization: Basic " . base64_encode("{$this->_username}:{$this->_password}")
                )
                ->withData($data)
                ->get();

            if ($update === false) {
                $this->_log->info(sprintf('Could not download update file "%s"!', $updateFile));
                return false;
            }
            // Parse update file
            $updateFileExtension = substr(strrchr($this->_updateFile, '.'), 1);
            switch ($updateFileExtension) {
                case 'ini':
                    $versions = @parse_ini_string($update, true);
                    if (!is_array($versions)) {
                        $this->_log->error('Unable to parse ini update file!');
                        return false;
                    }
                    $versions = array_map(function ($block) {
                        return isset($block['url']) ? $block['url'] : false;
                    }, $versions);
                    break;
                case 'json':
                    $versions = (array)@json_decode($update);
                    if (!is_array($versions)) {
                        $this->_log->error('Unable to parse json update file!');
                        return false;
                    }

                    if (isset($versions['result']) && 0 == $versions['result']) {
                        return $versions;
                    }

                    break;
                default:
                    $this->_log->error(sprintf('Unknown file extension "%s"', $updateFileExtension));
                    return false;
            }
            $this->_cache->put('update-versions', $versions);
        } else {
            $this->_log->debug('Got updates from cache');
        }
        if (!is_array($versions)) {
            $this->_log->error(sprintf('Could not read versions from server %s', $updateFile));
            return false;
        }

        // Check for latest version
        foreach ($versions as $versionRaw => $updateUrl) {
           // $this->checkDomain($updateUrl->domain);

            $version = new version($versionRaw);

            if ($version->valid() === null) {
                $this->_log->info(sprintf('Could not parse version "%s" from update server "%s"', $versionRaw, $updateFile));
                continue;
            }

            if (version::gt($version, $this->_currentVersion)) {
                if (version::gt($version, $this->_latestVersion))
                    $this->_latestVersion = $version;

                $this->_updates[] = [
                    'version' => $version->getVersion(),
                    'url'     => $updateUrl->url,
                    'description' => $updateUrl->description,
                    'created_at' => strtotime($updateUrl->created_at->date),
                    'upgrade'  => $updateUrl->upgrade,
                    'php_version' => $updateUrl->php_version
                ];
            }
        }

        // Sort versions to install
        usort($this->_updates, function ($a, $b) {
            return version::compare($a['version'], $b['version']);
        });
        if ($this->newVersionAvailable()) {
            $this->_log->debug(sprintf('New version "%s" available', $this->_latestVersion));
            return true;
        } else {
            $this->_log->debug('No new version available');
            return self::NO_UPDATE_AVAILABLE;
        }
    }

    /*
     * 检测指定的 key 和 密钥是否存在
     *
     *
     * @params string $fileName 检查路径
     * @params array $keyAndSecret ['key' => string, 'secret' => string]
     * @params array $postData post 传参
     *
     * @return mixed
     */
    public function isKeySecretExists($fileName, $keyAndSecret, $postData, $message='') {
        //dd($fileName . "<br/>" . $keyAndSecret['key'] . '==> ' . $keyAndSecret['secret'] . '<br/>' . $postData . "<br/>" . $message);
        $content = Curl::to($fileName)
            ->withHeader(
                "Authorization: Basic " . base64_encode("{$keyAndSecret['key']}:{$keyAndSecret['secret']}")
            )
            ->withData($postData)
            ->get();
        //var_dump($content);exit();
        $result = json_decode($content, true);
        if(!$result['isExists']) {
            $this->_log->error($message . $result['message']);
        }
        return $result;
    }

    /**
     * Check if a new version is available.
     *
     * @return bool
     */
    public function newVersionAvailable()
    {
        if (!empty($this->_updates) && $this->_updates['upgrade'] != 'master') {
            return true;
        }
        return version::gt($this->_latestVersion, $this->_currentVersion);
    }
    /**
     * Download the update
     *
     * @param string $updateUrl Url where to download from
     * @param string $updateFile Path where to save the download
     *
     * @return bool
     */
    protected function _downloadUpdate($updateUrl, $updateFile)
    {
        $this->_log->info(sprintf('Downloading update "%s" to "%s"', $updateUrl, $updateFile));

        return Curl::to($updateUrl)
            ->withHeader(
                "Authorization: Basic " . base64_encode("{$this->_username}:{$this->_password}")
            )
            ->withContentType('application/zip, application/octet-stream')
            ->withOption('FOLLOWLOCATION',true)
            ->withOption('TIMEOUT',100)
            ->download($updateFile);
    }

    protected function _downloadUpdate_v2($updateUrl, $updateFile, $client)
    {
        $this->_log->info(sprintf('Downloading update "%s" to "%s"', $updateUrl, $updateFile));

        //获取文件夹数据
        $checkUpdateFileUurl = $updateUrl . '/check/' . $client . '/0';

        $files = Curl::to($checkUpdateFileUurl)
            ->withHeader(
                "Authorization: Basic " . base64_encode("{$this->_username}:{$this->_password}")
            )
            ->asJsonResponse(true)
            ->get();

        if (!is_null($files) && !empty($files['result'])) {
            $downloadUrl = $updateUrl . '/download/';

            foreach ($files['result'] as $item) {
                $updateUrl =  $downloadUrl . $client . '/' . $item;
                $updateFile = $this->_tempDir . $item;

                Curl::to($updateUrl)
                    ->withHeader(
                        "Authorization: Basic " . base64_encode("{$this->_username}:{$this->_password}")
                    )
                    ->withContentType('application/zip, application/octet-stream')
                    ->withOption('FOLLOWLOCATION',true)
                    ->withOption('TIMEOUT',100)
                    ->download($updateFile);

            }
        }

        return true;
    }
    /**
     * Simulate update process.
     *
     * @param string $updateFile
     *
     * @return bool
     */
    protected function _simulateInstall($updateFile)
    {
        $this->_log->notice('[SIMULATE] Install new version');
        clearstatcache();
        // Check if zip file could be opened
        $zip = zip_open($updateFile);
        if (!is_resource($zip)) {
            $this->_log->error(sprintf('Could not open zip file "%s", error: %d', $updateFile, $zip));
            return false;
        }
        $i = -1;
        $files = [];
        $simulateSuccess = true;
        while ($file = zip_read($zip)) {
            $i++;
            $filename = zip_entry_name($file);
            $foldername = $this->_installDir . dirname($filename);
            $absoluteFilename = $this->_installDir . $filename;
            $files[$i] = [
                'filename'          => $filename,
                'foldername'        => $foldername,
                'absolute_filename' => $absoluteFilename,
            ];
            $this->_log->debug(sprintf('[SIMULATE] Updating file "%s"', $filename));
            // Check if parent directory is writable
            if (!is_dir($foldername)) {
                $this->_log->debug(sprintf('[SIMULATE] Create directory "%s"', $foldername));
                $files[$i]['parent_folder_exists'] = false;
                $parent = dirname($foldername);
                if(!is_dir($parent)){
                    if (!mkdir($parent, $this->dirPermissions, true)) {
                        $files[$i]['parent_folder_writable'] = false;
                        $simulateSuccess = false;
                        $this->_log->error(sprintf('Directory "%s" has to be writeable!', $parent));
                    }
                }
                if (!is_writable($parent)) {
                    $files[$i]['parent_folder_writable'] = false;
                    $simulateSuccess = false;
                    $this->_log->warning(sprintf('[SIMULATE] Directory "%s" has to be writeable!', $parent));
                } else {
                    $files[$i]['parent_folder_writable'] = true;
                }
            }
            // Skip if entry is a directory
            if (substr($filename, -1, 1) == DIRECTORY_SEPARATOR || substr($filename, -1, 1) == '.')
                continue;
            // Read file contents from archive
            $contents = zip_entry_read($file, zip_entry_filesize($file));
            if ($contents === false) {
                $files[$i]['extractable'] = false;
                $simulateSuccess = false;
                $this->_log->warning(sprintf('[SIMULATE] Coud not read contents of file "%s" from zip file!', $filename));
            }
            // Write to file
            if (file_exists($absoluteFilename)) {
                $files[$i]['file_exists'] = true;
                if (!is_writable($absoluteFilename)) {
                    $files[$i]['file_writable'] = false;
                    $simulateSuccess = false;
                    $this->_log->warning(sprintf('[SIMULATE] Could not overwrite "%s"!', $absoluteFilename));
                }
            } else {
                $files[$i]['file_exists'] = false;
                if (is_dir($foldername)) {
                    if (!is_writable($foldername)) {
                        $files[$i]['file_writable'] = false;
                        $simulateSuccess = false;
                        $this->_log->warning(sprintf('[SIMULATE] The file "%s" could not be created!', $absoluteFilename));
                    } else {
                        $files[$i]['file_writable'] = true;
                    }
                } else {
                    $files[$i]['file_writable'] = true;
                    $this->_log->debug(sprintf('[SIMULATE] The file "%s" could be created', $absoluteFilename));
                }
            }
            if ($filename == $this->updateScriptName) {
                $this->_log->debug(sprintf('[SIMULATE] Update script "%s" found', $absoluteFilename));
                $files[$i]['update_script'] = true;
            } else {
                $files[$i]['update_script'] = false;
            }
        }
        $this->_simulationResults = $files;
        return $simulateSuccess;
    }

    protected function _simulateInstall_v2($updateFile, $version, $client = 1)
    {
        $this->_log->notice('[SIMULATE] Install new version');
        clearstatcache();

        // Check if zip file could be opened
        $dir = $this->_tempDir . $version;

        if (!is_dir($dir)) {
            $this->_log->error(sprintf('Could not open dir "%s", error: %d', $version, $dir));
            return false;
        }

        $files = [];
        $simulateSuccess = true;

        if (is_dir($dir)) {
            $allfiles = app(Filesystem::class)->allFiles($dir);

            foreach ($allfiles as $key => $rows) {
                $filename = $rows->getRelativePathname();
                $foldername = $this->_installDir . dirname($filename);
                $absoluteFilename = $this->_installDir . $filename;

                if (2 == $client || (1 == $client && config('app.framework') == 'platform')) {
                    $foldername = $this->_installDir . 'addons/yun_shop/' . dirname($filename);
                    $absoluteFilename = $this->_installDir . 'addons/yun_shop/' . $filename;
                }

                $files[$key] = [
                    'filename'          => $filename,
                    'foldername'        => $foldername,
                    'absolute_filename' => $absoluteFilename,
                ];

                $this->_log->debug(sprintf('[SIMULATE] Updating file "%s"', $filename));

                // Check if parent directory is writable
                if (!is_dir($foldername)) {
                    $this->_log->debug(sprintf('[SIMULATE] Create directory "%s"', $foldername));
                    $files[$key]['parent_folder_exists'] = false;
                    $parent = dirname($foldername);

                    if(!is_dir($parent)){
                        if (!mkdir($parent, $this->dirPermissions, true)) {
                            $files[$key]['parent_folder_writable'] = false;
                            $simulateSuccess = false;
                            $this->_log->error(sprintf('Directory "%s" has to be writeable!', $parent));
                        }
                    }

                    if (!is_writable($parent)) {
                        $files[$key]['parent_folder_writable'] = false;
                        $simulateSuccess = false;
                        $this->_log->warning(sprintf('[SIMULATE] Directory "%s" has to be writeable!', $parent));
                    } else {
                        $files[$key]['parent_folder_writable'] = true;
                    }
                }

                // Skip if entry is a directory
                if (substr($filename, -1, 1) == DIRECTORY_SEPARATOR || substr($filename, -1, 1) == '.')
                    continue;

                // Read file contents from archive
                $contents = file_get_contents($rows->getPathname());
                if ($contents === false) {
                    $files[$key]['extractable'] = false;
                    $simulateSuccess = false;
                    $this->_log->warning(sprintf('[SIMULATE] Coud not read contents of file "%s" from zip file!', $filename));
                }
                // Write to file
                if (file_exists($absoluteFilename)) {
                    $files[$key]['file_exists'] = true;
                    if (!is_writable($absoluteFilename)) {
                        $files[$key]['file_writable'] = false;
                        $simulateSuccess = false;
                        $this->_log->warning(sprintf('[SIMULATE] Could not overwrite "%s"!', $absoluteFilename));
                    }
                } else {
                    $files[$key]['file_exists'] = false;
                    if (is_dir($foldername)) {
                        if (!is_writable($foldername)) {
                            $files[$key]['file_writable'] = false;
                            $simulateSuccess = false;
                            $this->_log->warning(sprintf('[SIMULATE] The file "%s" could not be created!', $absoluteFilename));
                        } else {
                            $files[$key]['file_writable'] = true;
                        }
                    } else {
                        $files[$key]['file_writable'] = true;
                        $this->_log->debug(sprintf('[SIMULATE] The file "%s" could be created', $absoluteFilename));
                    }
                }
                if ($filename == $this->updateScriptName) {
                    $this->_log->debug(sprintf('[SIMULATE] Update script "%s" found', $absoluteFilename));
                    $files[$key]['update_script'] = true;
                } else {
                    $files[$key]['update_script'] = false;
                }

            }
        }

        $this->_simulationResults = $files;
        return $simulateSuccess;
    }
    /**
     * Install update.
     *
     * @param string $updateFile Path to the update file
     * @param bool   $simulateInstall Check for directory and file permissions before copying files
     *
     * @return bool
     */
    protected function _install($updateFile, $simulateInstall, $version)
    {
        $this->_log->notice(sprintf('Trying to install update "%s"', $updateFile));
        // Check if install should be simulated
        if ($simulateInstall && !$this->_simulateInstall($updateFile)) {
            $this->_log->critical('Simulation of update process failed!');
            return self::ERROR_SIMULATE;
        }
        clearstatcache();
        // Check if zip file could be opened
        $zip = zip_open($updateFile);
        if (!is_resource($zip)) {
            $this->_log->error(sprintf('Could not open zip file "%s", error: %d', $updateFile, $zip));
            return false;
        }
        // Read every file from archive
        while ($file = zip_read($zip)) {
            $filename = zip_entry_name($file);
            $foldername = $this->_installDir . dirname($filename);
            $absoluteFilename = $this->_installDir . $filename;
            $this->_log->debug(sprintf('Updating file "%s"', $filename));
            if (!is_dir($foldername)) {
                if (!mkdir($foldername, $this->dirPermissions, true)) {
                    $this->_log->error(sprintf('Directory "%s" has to be writeable!', $foldername));
                    return false;
                }
            }
            // Skip if entry is a directory
            if (substr($filename, -1, 1) == '/' || substr($filename, -1, 1) == '\\' || substr($filename, -1, 1) == '.')
                continue;
            // Read file contents from archive
            $contents = zip_entry_read($file, zip_entry_filesize($file));
            if ($contents === false) {
                $this->_log->error(sprintf('Coud not read zip entry "%s"', $file));
                continue;
            }
            // Write to file
            if (file_exists($absoluteFilename)) {
                if (!is_writable($absoluteFilename)) {
                    $this->_log->error('Could not overwrite "%s"!', $absoluteFilename);
                    zip_close($zip);
                    return false;
                }
            } else {
                if (!touch($absoluteFilename)) {
                    $this->_log->error(sprintf('[SIMULATE] The file "%s" could not be created!', $absoluteFilename));
                    zip_close($zip);
                    return false;
                }
                $this->_log->debug(sprintf('File "%s" created', $absoluteFilename));
            }
            $updateHandle = @fopen($absoluteFilename, 'w');
            if (!$updateHandle) {
                $this->_log->error(sprintf('Could not open file "%s"!', $absoluteFilename));
                zip_close($zip);
                return false;
            }
            if (!empty($contents) && !fwrite($updateHandle, $contents)) {
                $this->_log->error(sprintf('Could not write to file "%s"!', $absoluteFilename));
                zip_close($zip);
                return false;
            }
            fclose($updateHandle);
            //If file is a update script, include
            if ($filename == $this->updateScriptName) {
                $this->_log->debug(sprintf('Try to include update script "%s"', $absoluteFilename));
                require($absoluteFilename);
                $this->_log->info(sprintf('Update script "%s" included!', $absoluteFilename));
                if (!unlink($absoluteFilename)) {
                    $this->_log->warning(sprintf('Could not delete update script "%s"!', $absoluteFilename));
                }
            }
        }
        zip_close($zip);
        // TODO
        $this->_log->notice(sprintf('Update "%s" successfully installed', $version));
        return true;
    }

    protected function _install_v2($updateFile, $simulateInstall, $version, $client = 1)
    {
        $this->_log->notice(sprintf('Trying to install update "%s"', $updateFile));
        // Check if install should be simulated
        if ($simulateInstall && !$this->_simulateInstall_v2($updateFile, $version, $client)) {
            $this->_log->critical('Simulation of update process failed!');
            return self::ERROR_SIMULATE;
        }

        clearstatcache();
        // Check if zip file could be opened
        $dir = $this->_tempDir . $version;

        if (is_dir($dir)) {
            $allfiles = app(Filesystem::class)->allFiles($dir);

            foreach ($allfiles as $rows) {
                $filename = $rows->getRelativePathname();
                $foldername = $this->_installDir . dirname($filename);
                $absoluteFilename = $this->_installDir . $filename;

                if (2 == $client || (1 == $client && config('app.framework') == 'platform')) {
                    $foldername = $this->_installDir . 'addons/yun_shop/' . dirname($filename);
                    $absoluteFilename = $this->_installDir . 'addons/yun_shop/' . $filename;
                }

                $this->_log->debug(sprintf('Updating file "%s"', $filename));
                if (!is_dir($foldername)) {
                    if (!mkdir($foldername, $this->dirPermissions, true)) {
                        $this->_log->error(sprintf('Directory "%s" has to be writeable!', $foldername));
                        return false;
                    }
                }

                // Skip if entry is a directory
                if (substr($filename, -1, 1) == '/' || substr($filename, -1, 1) == '\\' || substr($filename, -1, 1) == '.')
                    continue;

                // Read file contents from archive
                $contents = file_get_contents($rows->getPathname());
                if ($contents === false) {
                    $this->_log->error(sprintf('Coud not read zip entry "%s"', $filename));
                    continue;
                }

                // Write to file
                if (file_exists($absoluteFilename)) {
                    if (!is_writable($absoluteFilename)) {
                        $this->_log->error('Could not overwrite "%s"!', $absoluteFilename);
                        return false;
                    }
                } else {
                    if (!touch($absoluteFilename)) {
                        $this->_log->error(sprintf('[SIMULATE] The file "%s" could not be created!', $absoluteFilename));
                        return false;
                    }

                    $this->_log->debug(sprintf('File "%s" created', $absoluteFilename));
                }

                $updateHandle = @fopen($absoluteFilename, 'w');

                if (!$updateHandle) {
                    $this->_log->error(sprintf('Could not open file "%s"!', $absoluteFilename));
                    return false;
                }

                if (!empty($contents) && !fwrite($updateHandle, $contents)) {
                    $this->_log->error(sprintf('Could not write to file "%s"!', $absoluteFilename));
                    return false;
                }

                fclose($updateHandle);

                //If file is a update script, include
                if ($filename == $this->updateScriptName) {
                    $this->_log->debug(sprintf('Try to include update script "%s"', $absoluteFilename));
                    require($absoluteFilename);
                    $this->_log->info(sprintf('Update script "%s" included!', $absoluteFilename));
                    if (!unlink($absoluteFilename)) {
                        $this->_log->warning(sprintf('Could not delete update script "%s"!', $absoluteFilename));
                    }
                }
            }
        }

        // TODO
        $this->_log->notice(sprintf('Update "%s" successfully installed', $version));
        return true;
    }

    /**
     * Update to the latest version
     *
     * @param bool $simulateInstall Check for directory and file permissions before copying files (Default: true)
     * @param bool $deleteDownload Delete download after update (Default: true)
     *
     * @return mixed integer|bool
     */
    public function update($client = 1, $simulateInstall = true, $deleteDownload = true)
    {
        $this->_log->info('Trying to perform update');
        // Check for latest version
        if ($this->_latestVersion === null || count($this->_updates) === 0)
            $client == 2 ? $this->checkBackUpdate() : $this->checkUpdate();
        if ($this->_latestVersion === null || count($this->_updates) === 0) {
            $this->_log->error('Could not get latest version from server!');
            return self::ERROR_VERSION_CHECK;
        }

        // Check if current version is up to date
        if (!$this->newVersionAvailable()) {
            $this->_log->warning('No update available!');
            return self::NO_UPDATE_AVAILABLE;
        }

        rsort($this->_updates);
        foreach ($this->_updates as $key => $update) {
            if ($key > 0) {
                break;
            }

            $this->_log->debug(sprintf('Update to version "%s"', $update['version']));
            // Check for temp directory
            if (empty($this->_tempDir) || !is_dir($this->_tempDir) || !is_writable($this->_tempDir)) {
                $this->_log->critical(sprintf('Temporary directory "%s" does not exist or is not writeable!', $this->_tempDir));
                return self::ERROR_TEMP_DIR;
            }
            // Check for install directory
            if (empty($this->_installDir) || !is_dir($this->_installDir) || !is_writable($this->_installDir)) {
                $this->_log->critical(sprintf('Install directory "%s" does not exist or is not writeable!', $this->_installDir));
                return self::ERROR_INSTALL_DIR;
            }

            $updateFile = $this->_tempDir . $update['version'] . '.zip';

            // Download update
            if (!is_file($updateFile)) {
                if (!$this->_downloadUpdate_v2($update['url'], $updateFile, $client)) {
                    $this->_log->critical(sprintf('Failed to download update from "%s" to "%s"!', $update['url'], $updateFile));
                    return self::ERROR_DOWNLOAD_UPDATE;
                }
                $this->_log->debug(sprintf('Latest update downloaded to "%s"', $updateFile));
            } else {
                $this->_log->info(sprintf('Latest update already downloaded to "%s"', $updateFile));
            }

            //下载文件MD5校验
            if (file_exists($this->_tempDir . 'md5.txt')) {
                $error = [];
                $md5 = file_get_contents($this->_tempDir . 'md5.txt');
                $segment = explode(PHP_EOL, $md5);

                foreach ($segment as $val) {
                    if (!empty($val)) {
                        $item = str_replace("\r", '', $val);

                        $rows = explode(':', $item);
                        $file_md5[$rows[0]] = $rows[1];
                    }
                }

                $allfiles = app(Filesystem::class)->allFiles($this->_tempDir);

                if (empty($allfiles)) {
                    return '更新文件不存在';
                }

                foreach ($allfiles as $file) {
                    $soure_file[$file->getFilename()] = md5_file($file->getRealPath());
                }

                foreach ($file_md5 as $k => $v) {
                    if (!is_null($v) && !empty($soure_file[$k]) && $v != $soure_file[$k]) {
                        $error[$k] = ['source' => $v, 'destination' => $soure_file[$k]];
                    }
                }
                $this->_log->debug('Download zip file successfull');

                if (empty($error)) {
                    $default_dir = $client == 2 ? 'framework_frontend' :'frontend';

                    // Install update
                    $yZip = new YZip();
                    $yZip->unzip($this->_tempDir, $this->_tempDir);

                    $chk_url = substr(config('auto-update.checkUrl'), strpos(config('auto-update.checkUrl'), '/')+2);
                    $chk_url = substr($chk_url, 0, strpos($chk_url, '/'));
                    $cp_source_path = 'app/auto-update/temp/data/wwwroot/' . $chk_url . '/storage/' . $update['upgrade'] . '/' . $default_dir . '/' . $update['version'] . '_source/';
                    $cp_destination_path = 'app/auto-update/temp/' . $update['version'] . '/';

                    if (2 == $client) {
                        //copy 框架index.html到根目录后并删除
                        if (is_dir(storage_path($cp_source_path))) {
                            $cp_index_path = $cp_source_path  . '/index/index.html';
                            $cp_des_path   = base_path() . '/index.html';

                            app(Filesystem::class)->copy(storage_path($cp_index_path), $cp_des_path);
                            app(Filesystem::class)->delete(storage_path($cp_index_path));
                            app(Filesystem::class)->deleteDirectory(storage_path($cp_index_path . '/index'));
                        }
                    }

                    if (is_dir(storage_path($cp_source_path))) {
                        \Log::debug('copy file start.....', $cp_source_path);
                        app(Filesystem::class)->copyDirectory(storage_path($cp_source_path),
                                                                    storage_path($cp_destination_path));

                        app(Filesystem::class)->copy(storage_path($cp_destination_path . '/index/index.html'), storage_path($cp_destination_path . '/index.html'));
                        app(Filesystem::class)->delete(storage_path($cp_destination_path . '/index/index.html'));
                        app(Filesystem::class)->deleteDirectory(storage_path($cp_destination_path . '/index'));

                        \Log::debug('copy file end.....');
                    }

                    $result = $this->_install_v2($updateFile, $simulateInstall, $update['version'], $client);

                    if ($result === true) {
                        $this->runOnEachUpdateFinishCallbacks($update['version']);
                        if ($deleteDownload) {
                            $this->_log->debug(sprintf('Trying to delete update file "%s" after successfull update', $updateFile));
                            if (@$this->deldir($this->_tempDir)) {
                                $this->_log->info(sprintf('Update file "%s" deleted after successfull update', $updateFile));
                            } else {
                                $this->_log->error(sprintf('Could not delete update file "%s" after successfull update!', $updateFile));
                                return self::ERROR_DELETE_TEMP_UPDATE;
                            }
                        }
                    } else {
                        if ($deleteDownload) {
                            $this->_log->debug(sprintf('Trying to delete update file "%s" after failed update', $updateFile));
                            if (@$this->deldir($this->_tempDir)) {
                                $this->_log->info(sprintf('Update file "%s" deleted after failed update', $updateFile));
                            } else {
                                $this->_log->error(sprintf('Could not delete update file "%s" after failed update!', $updateFile));
                            }
                        }
                        return $result;
                    }

                    $this->runOnAllUpdateFinishCallbacks($this->getVersionsToUpdate());
                    return true;
                } else {
                    \Log::debug('-----下载文件校验失败-----', $error);
                    return '下载文件校验失败';
                }
            } else {
                return '校验文件不存在';
            }
        }
    }
    /**
     * Add slash at the end of the path.
     *
     * @param string $dir
     * @return string
     */
    public function addTrailingSlash($dir)
    {
        if (substr($dir, -1) != DIRECTORY_SEPARATOR)
            $dir = $dir . DIRECTORY_SEPARATOR;
        return $dir;
    }
    /**
     * @param array $callback
     */
    public function onEachUpdateFinish($callback)
    {
        $this->onEachUpdateFinishCallbacks[] = $callback;
    }
    /**
     * @param array $callback
     */
    public function setOnAllUpdateFinishCallbacks($callback)
    {
        $this->onAllUpdateFinishCallbacks[] = $callback;
    }
    public function runOnEachUpdateFinishCallbacks($updateVersion)
    {
        foreach ($this->onEachUpdateFinishCallbacks as $callback) {
            call_user_func($callback, $updateVersion);
        }
    }
    public function runOnAllUpdateFinishCallbacks($updatedVersions)
    {
        foreach ($this->onAllUpdateFinishCallbacks as $callback) {
            call_user_func($callback, $updatedVersions);
        }
    }

    public function checkBackUpdate()
    {
        $this->_log->notice('Back Checking for a new update...');

        $versions = $this->_cache->get('update-versions');
        // Create absolute url to update file
        $updateFile = $this->_updateUrl . '/' . $this->_updateFile;

        // Check if cache is empty
        if ($versions === null || $versions === false) {
            $this->_log->debug(sprintf('Get new updates from %s', $updateFile));
            // Read update file from update server
            //$update = @file_get_contents($updateFile, $this->_useBasicAuth());

            $data = [
                'plugins' => $this->getDirsByPath('plugins'),
                'vendor'  => $this->getDirsByPath('vendor'),
                'domain'  => rtrim(request()->getHost(), '/')
            ];

            $update = Curl::to($updateFile)
                ->withHeader(
                    "Authorization: Basic " . base64_encode("{$this->_username}:{$this->_password}")
                )
                ->withData($data)
                ->asJsonResponse(true)
                ->get();

            if (is_array($update)) {
               // return $this->checkDomain($update->domain);
            }

            if ($update === false) {
                $this->_log->info(sprintf('Could not download update file "%s"!', $updateFile));
                return false;
            }

            return $update;
        }
    }

    public function checkBackDownload($data)
    {
        $this->_log->notice('Back Checking for a new download...');

        $versions = $this->_cache->get('update-versions');
        // Create absolute url to update file
        $updateFile = $this->_updateUrl . '/' . $this->_updateFile;

        // Check if cache is empty
        if ($versions === null || $versions === false) {
            $this->_log->debug(sprintf('Get new updates from %s', $updateFile));

            $download = Curl::to($updateFile)
                ->withHeader(
                    "Authorization: Basic " . base64_encode("{$this->_username}:{$this->_password}")
                )
                ->withData($data)
                ->asJsonResponse(true)
                ->get();

            if ($download === false) {
                $this->_log->info(sprintf('Could not download update file "%s"!', $updateFile));
                return false;
            }

            return $download;
        }
    }

    public function getDirsByPath($path, Filesystem $filesystem = null)
    {
        $dirs = [];

        if (is_null($filesystem)) {
            $filesystem = app(Filesystem::class);
        }

        if ($all_dir = $filesystem->directories(base_path($path))) {
            if (!is_null($all_dir)) {
                foreach ($all_dir as $dir) {
                    $dirs[] = substr($dir, strrpos($dir, DIRECTORY_SEPARATOR)+1);
                }
            }
        }

        return $dirs;
    }

    private function checkDomain($domain)
    {
        if (!preg_match($_SERVER['HTTP_HOST'], $domain)) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                return 'unknown';
            }

            redirect(yzWebFullUrl('update.pirate'))->send();
        }
    }

    private function deldir($dir)
    {
        //先删除目录下的文件：
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . "/" . $file;
                if (!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    $this->deldir($fullpath);
                }
            }
        }

        closedir($dh);

        //删除当前文件夹：
        if (rmdir($dir)) {
            return true;
        } else {
            return false;

        }
    }

    public function isPluginExists($url, $message = '')
    {
        $content = Curl::to($url)
            ->asJsonResponse(true)
            ->get();

        if(!$content['isExists']) {
            $this->_log->error($message . $content['message']);
        }
        return $content;
    }
}
