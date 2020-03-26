<?php

/**
 * Common parameter parsing for benchmark and tests scripts.
 *
 * @param integer DEBUG
 * @param string  LOCALSERVER
 * @param string  URI
 * @param string  HTTPSSERVER
 * @param string  HTTPSSURI
 * @param string  PROXY
 * @param string  NOPROXY
 * @param bool    HTTPSIGNOREPEER
 * @param int     HTTPSVERIFYHOST
 * @param int     SSLVERSION
 *
 * @copyright (C) 2007-2015 G. Giunta
 * @license code licensed under the BSD License: see file license.txt
 **/
class argParser
{
    public static function getArgs()
    {
        global $argv;

        $args = array(
            'DEBUG' => 0,
            'LOCALSERVER' => 'localhost',
            'HTTPSSERVER' => 'gggeek.ssl.altervista.org',
            'HTTPSURI' => '/sw/xmlrpc/demo/server/server.php',
            'HTTPSIGNOREPEER' => false,
            'HTTPSVERIFYHOST' => 2,
            'SSLVERSION' => 0,
            'PROXYSERVER' => null,
            'NOPROXY' => false,
            'LOCALPATH' => __DIR__,
        );

        // check for command line vs web page input params
        if (!isset($_SERVER['REQUEST_METHOD'])) {
            if (isset($argv)) {
                foreach ($argv as $param) {
                    $param = explode('=', $param);
                    if (count($param) > 1) {
                        $pname = strtoupper(ltrim($param[0], '-'));
                        $$pname = $param[1];
                    }
                }
            }
        } else {
            // NB: we might as well consider using $_GET stuff later on...
            extract($_GET);
            extract($_POST);
        }

        if (isset($DEBUG)) {
            $args['DEBUG'] = intval($DEBUG);
        }
        if (isset($LOCALSERVER)) {
            $args['LOCALSERVER'] = $LOCALSERVER;
        } else {
            if (isset($HTTP_HOST)) {
                $args['LOCALSERVER'] = $HTTP_HOST;
            } elseif (isset($_SERVER['HTTP_HOST'])) {
                $args['LOCALSERVER'] = $_SERVER['HTTP_HOST'];
            }
        }
        if (isset($HTTPSSERVER)) {
            $args['HTTPSSERVER'] = $HTTPSSERVER;
        }
        if (isset($HTTPSURI)) {
            $args['HTTPSURI'] = $HTTPSURI;
        }
        if (isset($HTTPSIGNOREPEER)) {
            $args['HTTPSIGNOREPEER'] = (bool)$HTTPSIGNOREPEER;
        }
        if (isset($HTTPSVERIFYHOST)) {
            $args['HTTPSVERIFYHOST'] = (int)$HTTPSVERIFYHOST;
        }
        if (isset($SSLVERSION)) {
            $args['SSLVERSION'] = (int)$SSLVERSION;
        }
        if (isset($PROXY)) {
            $arr = explode(':', $PROXY);
            $args['PROXYSERVER'] = $arr[0];
            if (count($arr) > 1) {
                $args['PROXYPORT'] = $arr[1];
            } else {
                $args['PROXYPORT'] = 8080;
            }
        }
        // used to silence testsuite warnings about proxy code not being tested
        if (isset($NOPROXY)) {
            $args['NOPROXY'] = true;
        }
        if (!isset($URI)) {
            // GUESTIMATE the url of local demo server
            // play nice to php 3 and 4-5 in retrieving URL of server.php
            /// @todo filter out query string from REQUEST_URI
            if (isset($REQUEST_URI)) {
                $URI = str_replace('/tests/testsuite.php', '/demo/server/server.php', $REQUEST_URI);
                $URI = str_replace('/testsuite.php', '/server.php', $URI);
                $URI = str_replace('/tests/benchmark.php', '/demo/server/server.php', $URI);
                $URI = str_replace('/benchmark.php', '/server.php', $URI);
            } elseif (isset($_SERVER['PHP_SELF']) && isset($_SERVER['REQUEST_METHOD'])) {
                $URI = str_replace('/tests/testsuite.php', '/demo/server/server.php', $_SERVER['PHP_SELF']);
                $URI = str_replace('/testsuite.php', '/server.php', $URI);
                $URI = str_replace('/tests/benchmark.php', '/demo/server/server.php', $URI);
                $URI = str_replace('/benchmark.php', '/server.php', $URI);
            } else {
                $URI = '/demo/server/server.php';
            }
        }
        if ($URI[0] != '/') {
            $URI = '/' . $URI;
        }
        $args['URI'] = $URI;
        if (isset($LOCALPATH)) {
            $args['LOCALPATH'] = $LOCALPATH;
        }

        return $args;
    }
}
