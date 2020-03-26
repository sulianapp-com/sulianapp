<?php
/**
 * XMLRPC server acting as proxy for requests to other servers
 * (useful e.g. for ajax-originated calls that can only connect back to
 * the originating server).
 *
 * @author Gaetano Giunta
 * @copyright (C) 2006-2015 G. Giunta
 * @license code licensed under the BSD License: see file license.txt
 */

include_once __DIR__ . "/../../src/Autoloader.php";
PhpXmlRpc\Autoloader::register();

/**
 * Forward an xmlrpc request to another server, and return to client the response received.
 *
 * DO NOT RUN AS IS IN PRODUCTION - this is an open relay !!!
 *
 * @param PhpXmlRpc\Request $req (see method docs below for a description of the expected parameters)
 *
 * @return PhpXmlRpc\Response
 */
function forward_request($req)
{
    $encoder = new \PhpXmlRpc\Encoder();

    // create client
    $timeout = 0;
    $url = $encoder->decode($req->getParam(0));
    $client = new PhpXmlRpc\Client($url);

    if ($req->getNumParams() > 3) {
        // we have to set some options onto the client.
        // Note that if we do not untaint the received values, warnings might be generated...
        $options = $encoder->decode($req->getParam(3));
        foreach ($options as $key => $val) {
            switch ($key) {
                case 'Cookie':
                    break;
                case 'Credentials':
                    break;
                case 'RequestCompression':
                    $client->setRequestCompression($val);
                    break;
                case 'SSLVerifyHost':
                    $client->setSSLVerifyHost($val);
                    break;
                case 'SSLVerifyPeer':
                    $client->setSSLVerifyPeer($val);
                    break;
                case 'Timeout':
                    $timeout = (integer)$val;
                    break;
            } // switch
        }
    }

    // build call for remote server
    /// @todo find a way to forward client info (such as IP) to server, either
    /// - as xml comments in the payload, or
    /// - using std http header conventions, such as X-forwarded-for...
    $reqMethod = $encoder->decode($req->getParam(1));
    $pars = $req->getParam(2);
    $req = new PhpXmlRpc\Request($reqMethod);
    foreach ($pars as $par) {
        $req->addParam($par);
    }

    // add debug info into response we give back to caller
    PhpXmlRpc\Server::xmlrpc_debugmsg("Sending to server $url the payload: " . $req->serialize());

    return $client->send($req, $timeout);
}

// run the server
$server = new PhpXmlRpc\Server(
    array(
        'xmlrpcproxy.call' => array(
            'function' => 'forward_request',
            'signature' => array(
                array('mixed', 'string', 'string', 'array'),
                array('mixed', 'string', 'string', 'array', 'struct'),
            ),
            'docstring' => 'forwards xmlrpc calls to remote servers. Returns remote method\'s response. Accepts params: remote server url (might include basic auth credentials), method name, array of params, and (optionally) a struct containing call options',
        ),
    )
);
