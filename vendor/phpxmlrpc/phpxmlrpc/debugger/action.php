<?php
/**
 * @author Gaetano Giunta
 * @copyright (C) 2005-2015 G. Giunta
 * @license code licensed under the BSD License: see file license.txt
 *
 * @todo switch params for http compression from 0,1,2 to values to be used directly
 * @todo use ob_start to catch debug info and echo it AFTER method call results?
 * @todo be smarter in creating client stub for proxy/auth cases: only set appropriate property of client obj
 **/

header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>XMLRPC Debugger</title>
    <meta name="robots" content="index,nofollow"/>
    <style type="text/css">
        <!--
        body {
            border-top: 1px solid gray;
            padding: 1em;
            font-family: Verdana, Arial, Helvetica;
            font-size: 8pt;
        }

        h3 {
            font-size: 9.5pt;
        }

        h2 {
            font-size: 12pt;
        }

        .dbginfo {
            padding: 1em;
            background-color: #EEEEEE;
            border: 1px dashed silver;
            font-family: monospace;
        }

        #response {
            padding: 1em;
            margin-top: 1em;
            background-color: #DDDDDD;
            border: 1px solid gray;
            white-space: pre;
            font-family: monospace;
        }

        table {
            padding: 2px;
            margin-top: 1em;
        }

        th {
            background-color: navy;
            color: white;
            padding: 0.5em;
        }

        td {
            padding: 0.5em;
            font-family: monospace;
        }

        td form {
            margin: 0;
        }

        .oddrow {
            background-color: #EEEEEE;
        }

        .evidence {
            color: blue;
        }

        #phpcode {
            background-color: #EEEEEE;
            padding: 1em;
            margin-top: 1em;
        }

        -->
    </style>
</head>
<body>
<?php

include __DIR__ . '/common.php';
if ($action) {

    include_once __DIR__ . "/../src/Autoloader.php";
    PhpXmlRpc\Autoloader::register();

    // make sure the script waits long enough for the call to complete...
    if ($timeout) {
        set_time_limit($timeout + 10);
    }

    if ($wstype == 1) {
        @include 'jsonrpc.inc';
        if (!class_exists('jsonrpc_client')) {
            die('Error: to debug the jsonrpc protocol the jsonrpc.inc file is needed');
        }
        $clientClass = 'PhpJsRpc\Client';
        $requestClass = 'PhpJsRpc\Request';
        $protoName = 'JSONRPC';
    } else {
        $clientClass = 'PhpXmlRpc\Client';
        $requestClass = 'PhpXmlRpc\Request';
        $protoName = 'XMLRPC';
    }

    if ($port != "") {
        $client = new $clientClass($path, $host, $port);
        $server = "$host:$port$path";
    } else {
        $client = new $clientClass($path, $host);
        $server = "$host$path";
    }
    if ($protocol == 2) {
        $server = 'https://' . $server;
    } else {
        $server = 'http://' . $server;
    }
    if ($proxy != '') {
        $pproxy = explode(':', $proxy);
        if (count($pproxy) > 1) {
            $pport = $pproxy[1];
        } else {
            $pport = 8080;
        }
        $client->setProxy($pproxy[0], $pport, $proxyuser, $proxypwd);
    }

    if ($protocol == 2) {
        $client->setSSLVerifyPeer($verifypeer);
        $client->setSSLVerifyHost($verifyhost);
        if ($cainfo) {
            $client->setCaCertificate($cainfo);
        }
        $httpprotocol = 'https';
    } elseif ($protocol == 1) {
        $httpprotocol = 'http11';
    } else {
        $httpprotocol = 'http';
    }

    if ($username) {
        $client->setCredentials($username, $password, $authtype);
    }

    $client->setDebug($debug);

    switch ($requestcompression) {
        case 0:
            $client->request_compression = '';
            break;
        case 1:
            $client->request_compression = 'gzip';
            break;
        case 2:
            $client->request_compression = 'deflate';
            break;
    }

    switch ($responsecompression) {
        case 0:
            $client->accepted_compression = '';
            break;
        case 1:
            $client->accepted_compression = array('gzip');
            break;
        case 2:
            $client->accepted_compression = array('deflate');
            break;
        case 3:
            $client->accepted_compression = array('gzip', 'deflate');
            break;
    }

    $cookies = explode(',', $clientcookies);
    foreach ($cookies as $cookie) {
        if (strpos($cookie, '=')) {
            $cookie = explode('=', $cookie);
            $client->setCookie(trim($cookie[0]), trim(@$cookie[1]));
        }
    }

    $msg = array();
    switch ($action) {
        // fall thru intentionally
        case 'describe':
        case 'wrap':
            $msg[0] = new $requestClass('system.methodHelp', array(), $id);
            $msg[0]->addparam(new PhpXmlRpc\Value($method));
            $msg[1] = new $requestClass('system.methodSignature', array(), $id + 1);
            $msg[1]->addparam(new PhpXmlRpc\Value($method));
            $actionname = 'Description of method "' . $method . '"';
            break;
        case 'list':
            $msg[0] = new $requestClass('system.listMethods', array(), $id);
            $actionname = 'List of available methods';
            break;
        case 'execute':
            if (!payload_is_safe($payload)) {
                die("Tsk tsk tsk, please stop it or I will have to call in the cops!");
            }
            $msg[0] = new $requestClass($method, array(), $id);
            // hack! build xml payload by hand
            if ($wstype == 1) {
                $msg[0]->payload = "{\n" .
                    '"method": "' . $method . "\",\n\"params\": [" .
                    $payload .
                    "\n],\n\"id\": ";
                // fix: if user gave an empty string, use NULL, or we'll break json syntax
                if ($id == "") {
                    $msg[0]->payload .= "null\n}";
                } else {
                    if (is_numeric($id) || $id == 'false' || $id == 'true' || $id == 'null') {
                        $msg[0]->payload .= "$id\n}";
                    } else {
                        $msg[0]->payload .= "\"$id\"\n}";
                    }
                }
            } else {
                $msg[0]->payload = $msg[0]->xml_header($inputcharset) .
                    '<methodName>' . $method . "</methodName>\n<params>" .
                    $payload .
                    "</params>\n" . $msg[0]->xml_footer();
            }
            $actionname = 'Execution of method ' . $method;
            break;
        default: // give a warning
            $actionname = '[ERROR: unknown action] "' . $action . '"';
    }

    // Before calling execute, echo out brief description of action taken + date and time ???
    // this gives good user feedback for long-running methods...
    echo '<h2>' . htmlspecialchars($actionname, ENT_COMPAT, $inputcharset) . ' on server ' . htmlspecialchars($server, ENT_COMPAT, $inputcharset) . " ...</h2>\n";
    flush();

    $response = null;
    // execute method(s)
    if ($debug) {
        echo '<div class="dbginfo"><h2>Debug info:</h2>';
    }  /// @todo use ob_start instead
    $resp = array();
    $time = microtime(true);
    foreach ($msg as $message) {
        // catch errors: for older xmlrpc libs, send does not return by ref
        @$response = $client->send($message, $timeout, $httpprotocol);
        $resp[] = $response;
        if (!$response || $response->faultCode()) {
            break;
        }
    }
    $time = microtime(true) - $time;
    if ($debug) {
        echo "</div>\n";
    }

    if ($response) {
        if ($response->faultCode()) {
            // call failed! echo out error msg!
            //echo '<h2>'.htmlspecialchars($actionname, ENT_COMPAT, $inputcharset).' on server '.htmlspecialchars($server, ENT_COMPAT, $inputcharset).'</h2>';
            echo "<h3>$protoName call FAILED!</h3>\n";
            echo "<p>Fault code: [" . htmlspecialchars($response->faultCode(), ENT_COMPAT, \PhpXmlRpc\PhpXmlRpc::$xmlrpc_internalencoding) .
                "] Reason: '" . htmlspecialchars($response->faultString(), ENT_COMPAT, \PhpXmlRpc\PhpXmlRpc::$xmlrpc_internalencoding) . "'</p>\n";
            echo(strftime("%d/%b/%Y:%H:%M:%S\n"));
        } else {
            // call succeeded: parse results
            //echo '<h2>'.htmlspecialchars($actionname, ENT_COMPAT, $inputcharset).' on server '.htmlspecialchars($server, ENT_COMPAT, $inputcharset).'</h2>';
            printf("<h3>%s call(s) OK (%.2f secs.)</h3>\n", $protoName, $time);
            echo(strftime("%d/%b/%Y:%H:%M:%S\n"));

            switch ($action) {
                case 'list':

                    $v = $response->value();
                    if ($v->kindOf() == "array") {
                        $max = $v->count();
                        echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
                        echo "<thead>\n<tr><th>Method ($max)</th><th>Description</th></tr>\n</thead>\n<tbody>\n";
                        foreach($v as $i => $rec) {
                            if ($i % 2) {
                                $class = ' class="oddrow"';
                            } else {
                                $class = ' class="evenrow"';
                            }
                            echo("<tr><td$class>" . htmlspecialchars($rec->scalarval(), ENT_COMPAT, \PhpXmlRpc\PhpXmlRpc::$xmlrpc_internalencoding) . "</td><td$class><form action=\"controller.php\" method=\"get\" target=\"frmcontroller\">" .
                                "<input type=\"hidden\" name=\"host\" value=\"" . htmlspecialchars($host, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"port\" value=\"" . htmlspecialchars($port, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"path\" value=\"" . htmlspecialchars($path, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"id\" value=\"" . htmlspecialchars($id, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"debug\" value=\"$debug\" />" .
                                "<input type=\"hidden\" name=\"username\" value=\"" . htmlspecialchars($username, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"password\" value=\"" . htmlspecialchars($password, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"authtype\" value=\"$authtype\" />" .
                                "<input type=\"hidden\" name=\"verifyhost\" value=\"$verifyhost\" />" .
                                "<input type=\"hidden\" name=\"verifypeer\" value=\"$verifypeer\" />" .
                                "<input type=\"hidden\" name=\"cainfo\" value=\"" . htmlspecialchars($cainfo, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"proxy\" value=\"" . htmlspecialchars($proxy, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"proxyuser\" value=\"" . htmlspecialchars($proxyuser, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"proxypwd\" value=\"" . htmlspecialchars($proxypwd, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"responsecompression\" value=\"$responsecompression\" />" .
                                "<input type=\"hidden\" name=\"requestcompression\" value=\"$requestcompression\" />" .
                                "<input type=\"hidden\" name=\"clientcookies\" value=\"" . htmlspecialchars($clientcookies, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"protocol\" value=\"$protocol\" />" .
                                "<input type=\"hidden\" name=\"timeout\" value=\"" . htmlspecialchars($timeout, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"method\" value=\"" . htmlspecialchars($rec->scalarval(), ENT_COMPAT, \PhpXmlRpc\PhpXmlRpc::$xmlrpc_internalencoding) . "\" />" .
                                "<input type=\"hidden\" name=\"wstype\" value=\"$wstype\" />" .
                                "<input type=\"hidden\" name=\"action\" value=\"describe\" />" .
                                "<input type=\"hidden\" name=\"run\" value=\"now\" />" .
                                "<input type=\"submit\" value=\"Describe\" /></form></td>");
                            //echo("</tr>\n");

                            // generate the skeleton for method payload per possible tests
                            //$methodpayload="<methodCall>\n<methodName>".$rec->scalarval()."</methodName>\n<params>\n<param><value></value></param>\n</params>\n</methodCall>";

                            /*echo ("<form action=\"{$_SERVER['PHP_SELF']}\" method=\"get\"><td>".
                              "<input type=\"hidden\" name=\"host\" value=\"$host\" />".
                              "<input type=\"hidden\" name=\"port\" value=\"$port\" />".
                              "<input type=\"hidden\" name=\"path\" value=\"$path\" />".
                              "<input type=\"hidden\" name=\"method\" value=\"".$rec->scalarval()."\" />".
                              "<input type=\"hidden\" name=\"methodpayload\" value=\"$payload\" />".
                              "<input type=\"hidden\" name=\"action\" value=\"execute\" />".
                              "<input type=\"submit\" value=\"Test\" /></td></form>");*/
                            echo("</tr>\n");
                        }
                        echo "</tbody>\n</table>";
                    }
                    break;

                case 'describe':

                    $r1 = $resp[0]->value();
                    $r2 = $resp[1]->value();

                    echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
                    echo "<thead>\n<tr><th>Method</th><th>" . htmlspecialchars($method, ENT_COMPAT, $inputcharset) . "</th><th>&nbsp;</th><th>&nbsp;</th></tr>\n</thead>\n<tbody>\n";
                    $desc = htmlspecialchars($r1->scalarval(), ENT_COMPAT, \PhpXmlRpc\PhpXmlRpc::$xmlrpc_internalencoding);
                    if ($desc == "") {
                        $desc = "-";
                    }
                    echo "<tr><td class=\"evenrow\">Description</td><td colspan=\"3\" class=\"evenrow\">$desc</td></tr>\n";

                    if ($r2->kindOf() != "array") {
                        echo "<tr><td class=\"oddrow\">Signature</td><td class=\"oddrow\">Unknown</td><td class=\"oddrow\">&nbsp;</td></tr>\n";
                    } else {
                        foreach($r2 as $i => $x) {
                            $payload = "";
                            $alt_payload = "";
                            if ($i + 1 % 2) {
                                $class = ' class="oddrow"';
                            } else {
                                $class = ' class="evenrow"';
                            }
                            echo "<tr><td$class>Signature&nbsp;" . ($i + 1) . "</td><td$class>";
                            if ($x->kindOf() == "array") {
                                $ret = $x[0];
                                echo "<code>OUT:&nbsp;" . htmlspecialchars($ret->scalarval(), ENT_COMPAT, \PhpXmlRpc\PhpXmlRpc::$xmlrpc_internalencoding) . "<br />IN: (";
                                if ($x->count() > 1) {
                                    foreach($x as $k => $y) {
                                        if ($k == 0) continue;
                                        echo htmlspecialchars($y->scalarval(), ENT_COMPAT, \PhpXmlRpc\PhpXmlRpc::$xmlrpc_internalencoding);
                                        if ($wstype != 1) {
                                            $type = $y->scalarval();
                                            $payload .= '<param><value>';
                                            switch($type) {
                                                case 'undefined':
                                                    break;
                                                case 'null';
                                                    $type = 'nil';
                                                    // fall thru intentionally
                                                default:
                                                    $payload .= '<' .
                                                        htmlspecialchars($type, ENT_COMPAT, \PhpXmlRpc\PhpXmlRpc::$xmlrpc_internalencoding) .
                                                        '></' . htmlspecialchars($type, ENT_COMPAT, \PhpXmlRpc\PhpXmlRpc::$xmlrpc_internalencoding) .
                                                        '>';
                                            }
                                            $payload .= "</value></param>\n";
                                        }
                                        $alt_payload .= $y->scalarval();
                                        if ($k < $x->count() - 1) {
                                            $alt_payload .= ';';
                                            echo ", ";
                                        }
                                    }
                                }
                                echo ")</code>";
                            } else {
                                echo 'Unknown';
                            }
                            echo '</td>';
                            // button to test this method
                            //$payload="<methodCall>\n<methodName>$method</methodName>\n<params>\n$payload</params>\n</methodCall>";
                            echo "<td$class><form action=\"controller.php\" target=\"frmcontroller\" method=\"get\">" .
                                "<input type=\"hidden\" name=\"host\" value=\"" . htmlspecialchars($host, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"port\" value=\"" . htmlspecialchars($port, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"path\" value=\"" . htmlspecialchars($path, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"id\" value=\"" . htmlspecialchars($id, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"debug\" value=\"$debug\" />" .
                                "<input type=\"hidden\" name=\"username\" value=\"" . htmlspecialchars($username, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"password\" value=\"" . htmlspecialchars($password, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"authtype\" value=\"$authtype\" />" .
                                "<input type=\"hidden\" name=\"verifyhost\" value=\"$verifyhost\" />" .
                                "<input type=\"hidden\" name=\"verifypeer\" value=\"$verifypeer\" />" .
                                "<input type=\"hidden\" name=\"cainfo\" value=\"" . htmlspecialchars($cainfo, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"proxy\" value=\"" . htmlspecialchars($proxy, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"proxyuser\" value=\"" . htmlspecialchars($proxyuser, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"proxypwd\" value=\"" . htmlspecialchars($proxypwd, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"responsecompression\" value=\"$responsecompression\" />" .
                                "<input type=\"hidden\" name=\"requestcompression\" value=\"$requestcompression\" />" .
                                "<input type=\"hidden\" name=\"clientcookies\" value=\"" . htmlspecialchars($clientcookies, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"protocol\" value=\"$protocol\" />" .
                                "<input type=\"hidden\" name=\"timeout\" value=\"" . htmlspecialchars($timeout, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"method\" value=\"" . htmlspecialchars($method, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"methodpayload\" value=\"" . htmlspecialchars($payload, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"altmethodpayload\" value=\"" . htmlspecialchars($alt_payload, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"wstype\" value=\"$wstype\" />" .
                                "<input type=\"hidden\" name=\"action\" value=\"execute\" />";
                            if ($wstype != 1) {
                                echo "<input type=\"submit\" value=\"Load method synopsis\" />";
                            }
                            echo "</form></td>\n";

                            echo "<td$class><form action=\"controller.php\" target=\"frmcontroller\" method=\"get\">" .
                                "<input type=\"hidden\" name=\"host\" value=\"" . htmlspecialchars($host, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"port\" value=\"" . htmlspecialchars($port, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"path\" value=\"" . htmlspecialchars($path, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"id\" value=\"" . htmlspecialchars($id, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"debug\" value=\"$debug\" />" .
                                "<input type=\"hidden\" name=\"username\" value=\"" . htmlspecialchars($username, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"password\" value=\"" . htmlspecialchars($password, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"authtype\" value=\"$authtype\" />" .
                                "<input type=\"hidden\" name=\"verifyhost\" value=\"$verifyhost\" />" .
                                "<input type=\"hidden\" name=\"verifypeer\" value=\"$verifypeer\" />" .
                                "<input type=\"hidden\" name=\"cainfo\" value=\"" . htmlspecialchars($cainfo, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"proxy\" value=\"" . htmlspecialchars($proxy, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"proxyuser\" value=\"" . htmlspecialchars($proxyuser, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"proxypwd\" value=\"" . htmlspecialchars($proxypwd, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"responsecompression\" value=\"$responsecompression\" />" .
                                "<input type=\"hidden\" name=\"requestcompression\" value=\"$requestcompression\" />" .
                                "<input type=\"hidden\" name=\"clientcookies\" value=\"" . htmlspecialchars($clientcookies, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"protocol\" value=\"$protocol\" />" .
                                "<input type=\"hidden\" name=\"timeout\" value=\"" . htmlspecialchars($timeout, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"method\" value=\"" . htmlspecialchars($method, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"methodsig\" value=\"" . $i . "\" />" .
                                "<input type=\"hidden\" name=\"methodpayload\" value=\"" . htmlspecialchars($payload, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"altmethodpayload\" value=\"" . htmlspecialchars($alt_payload, ENT_COMPAT, $inputcharset) . "\" />" .
                                "<input type=\"hidden\" name=\"wstype\" value=\"$wstype\" />" .
                                "<input type=\"hidden\" name=\"run\" value=\"now\" />" .
                                "<input type=\"hidden\" name=\"action\" value=\"wrap\" />" .
                                "<input type=\"submit\" value=\"Generate method call stub code\" />";
                            echo "</form></td></tr>\n";
                        }
                    }
                    echo "</tbody>\n</table>";

                    break;

                case 'wrap':
                    $r1 = $resp[0]->value();
                    $r2 = $resp[1]->value();
                    if ($r2->kindOf() != "array" || $r2->count() <= $methodsig) {
                        echo "Error: signature unknown\n";
                    } else {
                        $mdesc = $r1->scalarval();
                        $encoder = new PhpXmlRpc\Encoder();
                        $msig = $encoder->decode($r2);
                        $msig = $msig[$methodsig];
                        $proto = $protocol == 2 ? 'https' : $protocol == 1 ? 'http11' : '';
                        if ($proxy == '' && $username == '' && !$requestcompression && !$responsecompression &&
                            $clientcookies == ''
                        ) {
                            $opts = 1; // simple client copy in stub code
                        } else {
                            $opts = 0; // complete client copy in stub code
                        }
                        if ($wstype == 1) {
                            $prefix = 'jsonrpc';
                        } else {
                            $prefix = 'xmlrpc';
                        }
                        $wrapper = new PhpXmlRpc\Wrapper();
                        $code = $wrapper->buildWrapMethodSource($client, $method, array('timeout' => $timeout, 'protocol' => $proto, 'simple_client_copy' => $opts, 'prefix' => $prefix), str_replace('.', '_', $prefix . '_' . $method), $msig, $mdesc);
                        //if ($code)
                        //{
                        echo "<div id=\"phpcode\">\n";
                        highlight_string("<?php\n" . $code['docstring'] . $code['source'] . '?>');
                        echo "\n</div>";
                        //}
                        //else
                        //{
                        //  echo 'Error while building php code stub...';
                    }

                    break;

                case 'execute':
                    echo '<div id="response"><h2>Response:</h2>' . htmlspecialchars($response->serialize()) . '</div>';
                    break;

                default: // give a warning
            }
        } // if !$response->faultCode()
    } // if $response
} else {
    // no action taken yet: give some instructions on debugger usage
    ?>

    <h3>Instructions on usage of the debugger</h3>
    <ol>
        <li>Run a 'list available methods' action against desired server</li>
        <li>If list of methods appears, click on 'describe method' for desired method</li>
        <li>To run method: click on 'load method synopsis' for desired method. This will load a skeleton for method call
            parameters in the form above. Complete all xmlrpc values with appropriate data and click 'Execute'
        </li>
    </ol>
    <?php
    if (!extension_loaded('curl')) {
        echo "<p class=\"evidence\">You will need to enable the CURL extension to use the HTTPS and HTTP 1.1 transports</p>\n";
    }
    ?>

    <h3>Example</h3>
    <p>
        Server Address: phpxmlrpc.sourceforge.net<br/>
        Path: /server.php
    </p>

    <h3>Notice</h3>
    <p>all usernames and passwords entered on the above form will be written to the web server logs of this server. Use
        with care.</p>

    <h3>Changelog</h3>
    <ul>
        <li>2015-05-30: fix problems with generating method payloads for NIL and Undefined parameters</li>
        <li>2015-04-19: fix problems with LATIN-1 characters in payload</li>
        <li>2007-02-20: add visual editor for method payload; allow strings, bools as jsonrpc msg id</li>
        <li>2006-06-26: support building php code stub for calling remote methods</li>
        <li>2006-05-25: better support for long running queries; check for no-curl installs</li>
        <li>2006-05-02: added support for JSON-RPC. Note that many interesting json-rpc features are not implemented
            yet, such as notifications or multicall.
        </li>
        <li>2006-04-22: added option for setting custom CA certs to verify peer with in SSLmode</li>
        <li>2006-03-05: added option for setting Basic/Digest/NTLM auth type</li>
        <li>2006-01-18: added option echoing to screen xmlrpc request before sending it ('More' debug)</li>
        <li>2005-10-01: added option for setting cookies to be sent to server</li>
        <li>2005-08-07: added switches for compression of requests and responses and http 1.1</li>
        <li>2005-06-27: fixed possible security breach in parsing malformed xml</li>
        <li>2005-06-24: fixed error with calling methods having parameters...</li>
    </ul>
<?php

}
?>
</body>
</html>
