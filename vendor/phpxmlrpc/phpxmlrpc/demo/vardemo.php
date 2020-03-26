<html>
<head><title>xmlrpc</title></head>
<body>
<?php

include_once __DIR__ . "/../vendor/autoload.php";

$req = new PhpXmlRpc\Request('examples.getStateName');

print "<h3>Testing value serialization</h3>\n";

$v = new PhpXmlRpc\Value(23, "int");
print "<PRE>" . htmlentities($v->serialize()) . "</PRE>";
$v = new PhpXmlRpc\Value("What are you saying? >> << &&");
print "<PRE>" . htmlentities($v->serialize()) . "</PRE>";

$v = new PhpXmlRpc\Value(array(
    new PhpXmlRpc\Value("ABCDEFHIJ"),
    new PhpXmlRpc\Value(1234, 'int'),
    new PhpXmlRpc\Value(1, 'boolean'),),
    "array"
);

print "<PRE>" . htmlentities($v->serialize()) . "</PRE>";

$v = new PhpXmlRpc\Value(
    array(
        "thearray" => new PhpXmlRpc\Value(
            array(
                new PhpXmlRpc\Value("ABCDEFHIJ"),
                new PhpXmlRpc\Value(1234, 'int'),
                new PhpXmlRpc\Value(1, 'boolean'),
                new PhpXmlRpc\Value(0, 'boolean'),
                new PhpXmlRpc\Value(true, 'boolean'),
                new PhpXmlRpc\Value(false, 'boolean'),
            ),
            "array"
        ),
        "theint" => new PhpXmlRpc\Value(23, 'int'),
        "thestring" => new PhpXmlRpc\Value("foobarwhizz"),
        "thestruct" => new PhpXmlRpc\Value(
            array(
                "one" => new PhpXmlRpc\Value(1, 'int'),
                "two" => new PhpXmlRpc\Value(2, 'int'),
            ),
            "struct"
        ),
    ),
    "struct"
);

print "<PRE>" . htmlentities($v->serialize()) . "</PRE>";

$w = new PhpXmlRpc\Value(array($v, new PhpXmlRpc\Value("That was the struct!")), "array");

print "<PRE>" . htmlentities($w->serialize()) . "</PRE>";

$w = new PhpXmlRpc\Value("Mary had a little lamb,
Whose fleece was white as snow,
And everywhere that Mary went
the lamb was sure to go.

Mary had a little lamb
She tied it to a pylon
Ten thousand volts went down its back
And turned it into nylon", "base64"
);
print "<PRE>" . htmlentities($w->serialize()) . "</PRE>";
print "<PRE>Value of base64 string is: '" . $w->scalarval() . "'</PRE>";

$req->method('');
$req->addParam(new PhpXmlRpc\Value("41", "int"));

print "<h3>Testing request serialization</h3>\n";
$op = $req->serialize();
print "<PRE>" . htmlentities($op) . "</PRE>";

print "<h3>Testing ISO date format</h3><pre>\n";

$t = time();
$date = PhpXmlRpc\Helper\Date::iso8601Encode($t);
print "Now is $t --> $date\n";
print "Or in UTC, that is " . PhpXmlRpc\Helper\Date::iso8601Encode($t, 1) . "\n";
$tb = PhpXmlRpc\Helper\Date::iso8601Decode($date);
print "That is to say $date --> $tb\n";
print "Which comes out at " . PhpXmlRpc\Helper\Date::iso8601Encode($tb) . "\n";
print "Which was the time in UTC at " . PhpXmlRpc\Helper\Date::iso8601Encode($date, 1) . "\n";

print "</pre>\n";

?>
</body>
</html>
