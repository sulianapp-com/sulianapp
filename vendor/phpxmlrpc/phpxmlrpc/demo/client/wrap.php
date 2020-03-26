<html>
<head><title>xmlrpc - Webservice wrappper demo</title></head>
<body>
<h1>Webservice wrappper demo</h1>

<h2>Wrap methods exposed by server into php functions</h2>

<h3>The code demonstrates usage of some the most automagic client usage possible:<br/>
    1) client that returns php values instead of xmlrpc value objects<br/>
    2) wrapping of remote methods into php functions<br/>
    See also proxy.php for an alternative take
</h3>
<?php

include_once __DIR__ . "/../../src/Autoloader.php";
PhpXmlRpc\Autoloader::register();

$client = new PhpXmlRpc\Client("http://phpxmlrpc.sourceforge.net/server.php");
$client->return_type = 'phpvals'; // let client give us back php values instead of xmlrpcvals
$resp = $client->send(new PhpXmlRpc\Request('system.listMethods'));
if ($resp->faultCode()) {
    echo "<p>Server methods list could not be retrieved: error {$resp->faultCode()} '" . htmlspecialchars($resp->faultString()) . "'</p>\n";
} else {
    echo "<p>Server methods list retrieved, now wrapping it up...</p>\n<ul>\n";
    flush();

    $callable = false;
    $wrapper = new PhpXmlRpc\Wrapper();
    foreach ($resp->value() as $methodName) {
        // $resp->value is an array of strings
        if ($methodName == 'examples.getStateName') {
            $callable = $wrapper->wrapXmlrpcMethod($client, $methodName);
            if ($callable) {
                echo "<li>Remote server method " . htmlspecialchars($methodName) . " wrapped into php function</li>\n";
            } else {
                echo "<li>Remote server method " . htmlspecialchars($methodName) . " could not be wrapped!</li>\n";
            }
            break;
        }
    }
    echo "</ul>\n";
    flush();

    if ($callable) {
        echo "Now testing function for remote method to convert U.S. state number into state name";
        $stateNum = rand(1, 51);
        // the 2nd parameter gets added to the closure - it is teh debug level to be used for the client
        $stateName = $callable($stateNum, 2);
    }
}
?>
</body>
</html>
