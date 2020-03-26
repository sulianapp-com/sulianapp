<html>
<head><title>xmlrpc - Getstatename demo</title></head>
<body>
<h1>Getstatename demo</h1>

<h2>Send a U.S. state number to the server and get back the state name</h2>

<h3>The code demonstrates usage of automatic encoding/decoding of php variables into xmlrpc values</h3>
<?php

include_once __DIR__ . "/../../src/Autoloader.php";
PhpXmlRpc\Autoloader::register();

if (isset($_POST["stateno"]) && $_POST["stateno"] != "") {
    $stateNo = (integer)$_POST["stateno"];
    $encoder = new PhpXmlRpc\Encoder();
    $req = new PhpXmlRpc\Request('examples.getStateName',
        array($encoder->encode($stateNo))
    );
    print "Sending the following request:<pre>\n\n" . htmlentities($req->serialize()) . "\n\n</pre>Debug info of server data follows...\n\n";
    $client = new PhpXmlRpc\Client("http://phpxmlrpc.sourceforge.net/server.php");
    $client->setDebug(1);
    $r = $client->send($req);
    if (!$r->faultCode()) {
        $v = $r->value();
        print "<br/>State number <b>" . $stateNo . "</b> is <b>"
            . htmlspecialchars($encoder->decode($v)) . "</b><br/>";
    } else {
        print "An error occurred: ";
        print "Code: " . htmlspecialchars($r->faultCode())
            . " Reason: '" . htmlspecialchars($r->faultString()) . "'</pre><br/>";
    }
} else {
    $stateNo = "";
}

print "<form action=\"getstatename.php\" method=\"POST\">
<input name=\"stateno\" value=\"" . $stateNo . "\"><input type=\"submit\" value=\"go\" name=\"submit\"></form>
<p>Enter a state number to query its name</p>";

?>
</body>
</html>
