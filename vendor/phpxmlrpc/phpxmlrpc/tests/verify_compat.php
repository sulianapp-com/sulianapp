<?php
/**
 * Verify compatibility level of current php install with php-xmlrpc lib.
 *
 * @author Gaetano Giunta
 * @copyright (C) 2006-2015 G. Giunta
 * @license code licensed under the BSD License: see file license.txt
 *
 * @todo add a test for php output buffering?
 */

function phpxmlrpc_verify_compat($mode = 'client')
{
    $tests = array();

    if ($mode == 'server') {
        // test for php version
        $ver = phpversion();
        $tests['php_version'] = array();
        $tests['php_version']['description'] = 'PHP version found: ' . $ver . ".\n\n";
        if (version_compare($ver, '5.3.0') < 0) {
            $tests['php_version']['status'] = 0;
            $tests['php_version']['description'] .= 'This version of PHP is not compatible with this release of the PHP XMLRPC library. Please upgrade to php 5.1.0 or later';
        } else {
            $tests['php_version']['status'] = 2;
            $tests['php_version']['description'] .= 'This version of PHP is fully compatible with the PHP XMLRPC library';
        }

        // test for zlib
        $tests['zlib'] = array();
        if (!function_exists('gzinflate')) {
            $tests['zlib']['status'] = 0;
            $tests['zlib']['description'] = "The zlib extension is not enabled.\n\nYou will not be able to receive compressed requests or send compressed responses, unless using the cURL library (for 'HTTPS' and 'HTTP 1.1' connections)";
        } else {
            $tests['zlib']['status'] = 2;
            $tests['zlib']['description'] = "The zlib extension is enabled.\n\nYou will be able to receive compressed requests and send compressed responses for the 'HTTP' protocol";
        }

        // test for dispaly of php errors in xml reponse
        if (ini_get('display_errors')) {
            if (intval(ini_get('error_reporting')) && E_NOTICE) {
                $tests['display_errors']['status'] = 1;
                $tests['display_errors']['description'] = "Error reporting level includes E_NOTICE errors, and display_errors is set to ON.\n\nAny error, warning or notice raised while executing php code exposed as xmlrpc method will result in an invalid xmlrpc response";
            } else {
                $tests['display_errors']['status'] = 1;
                $tests['display_errors']['description'] = "display_errors is set to ON.\n\nAny error raised while executing php code exposed as xmlrpc method will result in an invalid xmlrpc response";
            }
        }
    } else {

        // test for php version
        $ver = phpversion();
        $tests['php_version'] = array();
        $tests['php_version']['description'] = 'PHP version found: ' . $ver . ".\n\n";
        if (version_compare($ver, '5.3.0') < 0) {
            $tests['php_version']['status'] = 0;
            $tests['php_version']['description'] .= 'This version of PHP is not compatible with the PHP XMLRPC library. Please upgrade to 5.1.0 or later';
        } else {
            $tests['php_version']['status'] = 2;
            $tests['php_version']['description'] .= 'This version of PHP is fully compatible with the PHP XMLRPC library';
        }

        // test for zlib
        $tests['zlib'] = array();
        if (!function_exists('gzinflate')) {
            $tests['zlib']['status'] = 0;
            $tests['zlib']['description'] = "The zlib extension is not enabled.\n\nYou will not be able to send compressed requests or receive compressed responses, unless using the cURL library (for 'HTTPS' and 'HTTP 1.1' connections)";
        } else {
            $tests['zlib']['status'] = 2;
            $tests['zlib']['description'] = "The zlib extension is enabled.\n\nYou will be able to send compressed requests and receive compressed responses for the 'HTTP' protocol";
        }

        // test for CURL
        $tests['curl'] = array();
        if (!extension_loaded('curl')) {
            $tests['curl']['status'] = 0;
            $tests['curl']['description'] = "The cURL extension is not enabled.\n\nYou will not be able to send and receive messages using 'HTTPS' and 'HTTP 1.1' protocols";
        } else {
            $info = curl_version();
            $tests['curl']['status'] = 2;
            $tests['curl']['description'] = "The cURL extension is enabled.\n\nYou will be able to send and receive messages using 'HTTPS' and 'HTTP 1.1' protocols";
            if (version_compare($ver, '4.3.8') < 0) {
                $tests['curl']['status'] = 1;
                $tests['curl']['description'] .= ".\nPlease note that the current cURL install does not support keep-alives";
            }
            if (!((is_string($info) && strpos($info, 'zlib') !== null) || isset($info['libz_version']))) {
                $tests['curl']['status'] = 1;
                $tests['curl']['description'] .= ".\nPlease note that the current cURL install does not support compressed messages";
            }
            if (!((is_string($info) && strpos($info, 'OpenSSL') !== null) || isset($info['ssl_version']))) {
                $tests['curl']['status'] = 1;
                $tests['curl']['description'] .= ".\nPlease note that the current cURL install does not support HTTPS connections";
            }
        }
    }

    return $tests;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
    <title>PHP XMLRPC compatibility assessment</title>
    <style type="text/css">
        body, html {
            background-color: white;
            font-family: Arial, Verdana, Geneva, sans-serif;
            font-size: small;
        }

        table {
            border: 1px solid gray;
            padding: 0;
        }

        thead {
            background-color: silver;
            color: black;
        }

        td {
            margin: 0;
            padding: 0.5em;
        }

        tbody td {
            border-top: 1px solid gray;
        }

        .res0 {
            background-color: red;
            color: black;
            border-right: 1px solid gray;
        }

        .res1 {
            background-color: yellow;
            color: black;
            border-right: 1px solid gray;
        }

        .res2 {
            background-color: green;
            color: black;
            border-right: 1px solid gray;
        }

        .result {
            white-space: pre;
        }
    </style>
</head>
<body>
<h1>PHPXMLRPC compatibility assessment with the current PHP install</h1>
<h4>For phpxmlrpc version 4.0 or later</h4>

<h3>Server usage</h3>
<table cellspacing="0">
    <thead>
    <tr>
        <td>Test</td>
        <td>Result</td>
    </tr>
    </thead>
    <tbody>
    <?php
    $res = phpxmlrpc_verify_compat('server');
    foreach ($res as $test => $result) {
        echo '<tr><td class="res' . $result['status'] . '">' . htmlspecialchars($test) . '</td><td class="result">' . htmlspecialchars($result['description']) . "</td></tr>\n";
    }
    ?>
    </tbody>
</table>
<h3>Client usage</h3>
<table cellspacing="0">
    <thead>
    <tr>
        <td>Test</td>
        <td>Result</td>
    </tr>
    </thead>
    <tbody>
    <?php
    $res = phpxmlrpc_verify_compat('client');
    foreach ($res as $test => $result) {
        echo '<tr><td class="res' . $result['status'] . '">' . htmlspecialchars($test) . '</td><td class="result">' . htmlspecialchars($result['description']) . "</td></tr>\n";
    }
    ?>
    </tbody>
</table>
</body>
</html>
