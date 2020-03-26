<html>
<head><title>xmlrpc - Which toolkit demo</title></head>
<body>
<h1>Which toolkit demo</h1>
<h2>Query server for toolkit information</h2>
<h3>The code demonstrates usage of the PhpXmlRpc\Encoder class</h3>
<?php

include_once __DIR__ . "/../../src/Autoloader.php";
PhpXmlRpc\Autoloader::register();

$req = new PhpXmlRpc\Request('interopEchoTests.whichToolkit', array());
$client = new PhpXmlRpc\Client("http://phpxmlrpc.sourceforge.net/server.php");
$resp = $client->send($req);
if (!$resp->faultCode()) {
    $encoder = new PhpXmlRpc\Encoder();
    $value = $encoder->decode($resp->value());
    print "<pre>";
    print "name: " . htmlspecialchars($value["toolkitName"]) . "\n";
    print "version: " . htmlspecialchars($value["toolkitVersion"]) . "\n";
    print "docs: " . htmlspecialchars($value["toolkitDocsUrl"]) . "\n";
    print "os: " . htmlspecialchars($value["toolkitOperatingSystem"]) . "\n";
    print "</pre>";
} else {
    print "An error occurred: ";
    print "Code: " . htmlspecialchars($resp->faultCode()) . " Reason: '" . htmlspecialchars($resp->faultString()) . "'\n";
}
?>
</body>
</html>
