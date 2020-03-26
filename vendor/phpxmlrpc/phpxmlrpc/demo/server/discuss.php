<?php

include_once __DIR__ . "/../../vendor/autoload.php";

use PhpXmlRpc\Value;

$addComment_sig = array(array(Value::$xmlrpcInt, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcString));

$addComment_doc = 'Adds a comment to an item. The first parameter
is the item ID, the second the name of the commenter, and the third
is the comment itself. Returns the number of comments against that
ID.';

function addComment($req)
{
    $err = "";
    // since validation has already been carried out for us,
    // we know we got exactly 3 string values
    $encoder = new PhpXmlRpc\Encoder();
    $n = $encoder->decode($req);
    $msgID = $n[0];
    $name = $n[1];
    $comment = $n[2];

    $dbh = dba_open("/tmp/comments.db", "c", "db2");
    if ($dbh) {
        $countID = "${msgID}_count";
        if (dba_exists($countID, $dbh)) {
            $count = dba_fetch($countID, $dbh);
        } else {
            $count = 0;
        }
        // add the new comment in
        dba_insert($msgID . "_comment_${count}", $comment, $dbh);
        dba_insert($msgID . "_name_${count}", $name, $dbh);
        $count++;
        dba_replace($countID, $count, $dbh);
        dba_close($dbh);
    } else {
        $err = "Unable to open comments database.";
    }
    // if we generated an error, create an error return response
    if ($err) {
        return new PhpXmlRpc\Response(0, PhpXmlRpc\PhpXmlRpc::$xmlrpcerruser, $err);
    } else {
        // otherwise, we create the right response
        return new PhpXmlRpc\Response(new PhpXmlRpc\Value($count, "int"));
    }
}

$getComments_sig = array(array(Value::$xmlrpcArray, Value::$xmlrpcString));

$getComments_doc = 'Returns an array of comments for a given ID, which
is the sole argument. Each array item is a struct containing name
and comment text.';

function getComments($req)
{
    $err = "";
    $ra = array();
    $encoder = new PhpXmlRpc\Encoder();
    $msgID = $encoder->decode($req->getParam(0));
    $dbh = dba_open("/tmp/comments.db", "r", "db2");
    if ($dbh) {
        $countID = "${msgID}_count";
        if (dba_exists($countID, $dbh)) {
            $count = dba_fetch($countID, $dbh);
            for ($i = 0; $i < $count; $i++) {
                $name = dba_fetch("${msgID}_name_${i}", $dbh);
                $comment = dba_fetch("${msgID}_comment_${i}", $dbh);
                // push a new struct onto the return array
                $ra[] = array(
                    "name" => $name,
                    "comment" => $comment,
                );
            }
        }
    }
    // if we generated an error, create an error return response
    if ($err) {
        return new PhpXmlRpc\Response(0, PhpXmlRpc\PhpXmlRpc::$xmlrpcerruser, $err);
    } else {
        // otherwise, we create the right response
        return new PhpXmlRpc\Response($encoder->encode($ra));
    }
}

$srv = new PhpXmlRpc\Server(array(
    "discuss.addComment" => array(
        "function" => "addComment",
        "signature" => $addComment_sig,
        "docstring" => $addComment_doc,
    ),
    "discuss.getComments" => array(
        "function" => "getComments",
        "signature" => $getComments_sig,
        "docstring" => $getComments_doc,
    ),
));
