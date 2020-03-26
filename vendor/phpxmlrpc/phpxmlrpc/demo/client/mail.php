<?php
// Allow users to see the source of this file even if PHP is not configured for it
if (isset($_GET['showSource']) && $_GET['showSource']) {
    highlight_file(__FILE__);
    die();
}
?>
<html>
<head><title>xmlrpc - Mail demo</title></head>
<body>
<h1>Mail demo</h1>

<p>This form enables you to send mail via an XML-RPC server.
    When you press <kbd>Send</kbd> this page will reload, showing you the XML-RPC request sent to the host server, the
    XML-RPC response received and the internal evaluation done by the PHP implementation.</p>

<p>You can find the source to this page here: <a href="mail.php?showSource=1">mail.php</a><br/>
    And the source to a functionally identical mail-by-XML-RPC server in the file <a
        href="../server/server.php?showSource=1">server.php</a> included with the library (look for the 'mail_send'
    method)</p>
<?php

include_once __DIR__ . "/../../src/Autoloader.php";
PhpXmlRpc\Autoloader::register();

if (isset($_POST["mailto"]) && $_POST["mailto"]) {
    $server = "http://phpxmlrpc.sourceforge.net/server.php";
    $req = new PhpXmlRpc\Request('mail.send', array(
        new PhpXmlRpc\Value($_POST["mailto"]),
        new PhpXmlRpc\Value($_POST["mailsub"]),
        new PhpXmlRpc\Value($_POST["mailmsg"]),
        new PhpXmlRpc\Value($_POST["mailfrom"]),
        new PhpXmlRpc\Value($_POST["mailcc"]),
        new PhpXmlRpc\Value($_POST["mailbcc"]),
        new PhpXmlRpc\Value("text/plain")
    ));

    $client = new PhpXmlRpc\Client($server);
    $client->setDebug(2);
    $resp = $client->send($req);
    if (!$resp->faultCode()) {
        print "Mail sent OK<br/>\n";
    } else {
        print "<fonr color=\"red\">";
        print "Mail send failed<br/>\n";
        print "Fault: ";
        print "Code: " . htmlspecialchars($resp->faultCode()) .
            " Reason: '" . htmlspecialchars($resp->faultString()) . "'<br/>";
        print "</font><br/>";
    }
}
?>
<form method="POST">
    From <input size="60" name="mailfrom" value=""/><br/>
    <hr/>
    To <input size="60" name="mailto" value=""/><br/>
    Cc <input size="60" name="mailcc" value=""/><br/>
    Bcc <input size="60" name="mailbcc" value=""/><br/>
    <hr/>
    Subject <input size="60" name="mailsub" value="A message from xmlrpc"/>
    <hr/>
    Body <textarea rows="7" cols="60" name="mailmsg">Your message here</textarea><br/>
    <input type="Submit" value="Send"/>
</form>
</body>
</html>
