<?php

include_once __DIR__ . '/../lib/xmlrpc.inc';

include_once __DIR__ . '/parse_args.php';

/**
 * Tests involving requests sent to non-existing servers
 */
class InvalidHostTest extends PHPUnit_Framework_TestCase
{
    /** @var xmlrpc_client $client */
    public $client = null;
    public $args = array();

    public function setUp()
    {
        $this->args = argParser::getArgs();

        $this->client = new xmlrpc_client('/NOTEXIST.php', $this->args['LOCALSERVER'], 80);
        $this->client->setDebug($this->args['DEBUG']);

        if ($this->args['DEBUG'] == 1)
            ob_start();
    }

    protected function tearDown()
    {
        if ($this->args['DEBUG'] != 1)
            return;
        $out = ob_get_clean();
        $status = $this->getStatus();
        if ($status == PHPUnit_Runner_BaseTestRunner::STATUS_ERROR
            || $status == PHPUnit_Runner_BaseTestRunner::STATUS_FAILURE) {
            echo $out;
        }
    }

    public function test404()
    {
        $m = new xmlrpcmsg('examples.echo', array(
            new xmlrpcval('hello', 'string'),
        ));
        $r = $this->client->send($m, 5);
        $this->assertEquals(5, $r->faultCode());
    }

    public function testSrvNotFound()
    {
        $m = new xmlrpcmsg('examples.echo', array(
            new xmlrpcval('hello', 'string'),
        ));
        $this->client->server .= 'XXX';
        $r = $this->client->send($m, 5);
        // make sure there's no freaking catchall DNS in effect
        $dnsinfo = dns_get_record($this->client->server);
        if ($dnsinfo) {
            $this->markTestSkipped('Seems like there is a catchall DNS in effect: host ' . $this->client->server . ' found');
        } else {
            $this->assertEquals(5, $r->faultCode());
        }
    }

    public function testCurlKAErr()
    {
        if (!function_exists('curl_init')) {
            $this->markTestSkipped('CURL missing: cannot test curl keepalive errors');

            return;
        }
        $m = new xmlrpcmsg('examples.stringecho', array(
            new xmlrpcval('hello', 'string'),
        ));
        // test 2 calls w. keepalive: 1st time connection ko, second time ok
        $this->client->server .= 'XXX';
        $this->client->keepalive = true;
        $r = $this->client->send($m, 5, 'http11');
        // in case we have a "universal dns resolver" getting in the way, we might get a 302 instead of a 404
        $this->assertTrue($r->faultCode() === 8 || $r->faultCode() == 5);

        // now test a successful connection
        $server = explode(':', $this->args['LOCALSERVER']);
        if (count($server) > 1) {
            $this->client->port = $server[1];
        }
        $this->client->server = $server[0];
        $this->client->path = $this->args['URI'];

        $r = $this->client->send($m, 5, 'http11');
        $this->assertEquals(0, $r->faultCode());
        $ro = $r->value();
        is_object($ro) && $this->assertEquals('hello', $ro->scalarVal());
    }
}
