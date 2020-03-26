<?php

namespace PhpXmlRpc\Helper;

class Logger
{
    protected static $instance = null;

    /**
     * This class is singleton, so that later we can move to DI patterns.
     *
     * @return Logger
     */
    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Echoes a debug message, taking care of escaping it when not in console mode.
     * NB: if the encoding of the message is not known or wrong, and we are working in web mode, there is no guarantee
     *     of 100% accuracy, which kind of defeats the purpose of debugging
     *
     * @param string $message
     * @param string $encoding
     */
    public function debugMessage($message, $encoding=null)
    {
        // US-ASCII is a warning for PHP and a fatal for HHVM
        if ($encoding == 'US-ASCII') {
            $encoding = 'UTF-8';
        }

        if (PHP_SAPI != 'cli') {
            $flags = ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE;
            if ($encoding != null) {
                print "<PRE>\n".htmlentities($message, $flags, $encoding)."\n</PRE>";
            } else {
                print "<PRE>\n".htmlentities($message, $flags)."\n</PRE>";
            }
        } else {
            print "\n$message\n";
        }

        // let the user see this now in case there's a time out later...
        flush();
    }
}
