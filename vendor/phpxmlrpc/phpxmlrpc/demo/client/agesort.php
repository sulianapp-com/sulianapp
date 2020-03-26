<html>
<head><title>xmlrpc - Agesort demo</title></head>
<body>
<h1>Agesort demo</h1>

<h2>Send an array of 'name' => 'age' pairs to the server that will send it back sorted.</h2>

<h3>The source code demonstrates basic lib usage, including handling of xmlrpc arrays and structs</h3>

<p></p>
<?php

include_once __DIR__ . "/../../src/Autoloader.php";
PhpXmlRpc\Autoloader::register();

$inAr = array("Dave" => 24, "Edd" => 45, "Joe" => 37, "Fred" => 27);
print "This is the input data:<br/><pre>";
foreach($inAr as $key => $val) {
    print $key . ", " . $val . "\n";
}
print "</pre>";

// create parameters from the input array: an xmlrpc array of xmlrpc structs
$p = array();
foreach ($inAr as $key => $val) {
    $p[] = new PhpXmlRpc\Value(
        array(
            "name" => new PhpXmlRpc\Value($key),
            "age" => new PhpXmlRpc\Value($val, "int")
        ),
        "struct"
    );
}
$v = new PhpXmlRpc\Value($p, "array");
print "Encoded into xmlrpc format it looks like this: <pre>\n" . htmlentities($v->serialize()) . "</pre>\n";

// create client and message objects
$req = new PhpXmlRpc\Request('examples.sortByAge', array($v));
$client = new PhpXmlRpc\Client("http://phpxmlrpc.sourceforge.net/server.php");

// set maximum debug level, to have the complete communication printed to screen
$client->setDebug(2);

// send request
print "Now sending request (detailed debug info follows)";
$resp = $client->send($req);

// check response for errors, and take appropriate action
if (!$resp->faultCode()) {
    print "The server gave me these results:<pre>";
    $value = $resp->value();
    foreach ($value as $struct) {
        $name = $struct["name"];
        $age = $struct["age"];
        print htmlspecialchars($name->scalarval()) . ", " . htmlspecialchars($age->scalarval()) . "\n";
    }

    print "<hr/>For nerds: I got this value back<br/><pre>" .
        htmlentities($resp->serialize()) . "</pre><hr/>\n";
} else {
    print "An error occurred:<pre>";
    print "Code: " . htmlspecialchars($resp->faultCode()) .
        "\nReason: '" . htmlspecialchars($resp->faultString()) . '\'</pre><hr/>';
}

?>
</body>
</html>
