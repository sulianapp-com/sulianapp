<?php
/**
 * NB: do not let your IDE fool you. The correct encoding for this file is NOT UTF8.
 */
include_once __DIR__ . '/../lib/xmlrpc.inc';
include_once __DIR__ . '/../lib/xmlrpcs.inc';

include_once __DIR__ . '/parse_args.php';

/**
 * Tests involving parsing of xml and handling of xmlrpc values
 */
class ParsingBugsTests extends PHPUnit_Framework_TestCase
{
    public $args = array();

    protected function setUp()
    {
        $this->args = argParser::getArgs();
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

    protected function newMsg($methodName, $params = array())
    {
        $msg = new xmlrpcmsg($methodName, $params);
        $msg->setDebug($this->args['DEBUG']);
        return $msg;
    }

    public function testMinusOneString()
    {
        $v = new xmlrpcval('-1');
        $u = new xmlrpcval('-1', 'string');
        $t = new xmlrpcval(-1, 'string');
        $this->assertEquals($v->scalarval(), $u->scalarval());
        $this->assertEquals($v->scalarval(), $t->scalarval());
    }

    /**
     * This looks funny, and we might call it a bug. But we strive for 100 backwards compat...
     */
    public function testMinusOneInt()
    {
        $u = new xmlrpcval();
        $v = new xmlrpcval(-1);
        $this->assertEquals($u->scalarval(), $v->scalarval());
    }

    public function testUnicodeInMemberName()
    {
        $str = "G" . chr(252) . "nter, El" . chr(232) . "ne";
        $v = array($str => new xmlrpcval(1));
        $r = new xmlrpcresp(new xmlrpcval($v, 'struct'));
        $r = $r->serialize();
        $m = $this->newMsg('dummy');
        $r = $m->parseResponse($r);
        $v = $r->value();
        $this->assertEquals(true, $v->structmemexists($str));
    }

    public function testUnicodeInErrorString()
    {
        $response = utf8_encode(
            '<?xml version="1.0"?>
<!-- $Id -->
<!-- found by G. giunta, covers what happens when lib receives
  UTF8 chars in response text and comments -->
<!-- ' . chr(224) . chr(252) . chr(232) . '&#224;&#252;&#232; -->
<methodResponse>
<fault>
<value>
<struct>
<member>
<name>faultCode</name>
<value><int>888</int></value>
</member>
<member>
<name>faultString</name>
<value><string>' . chr(224) . chr(252) . chr(232) . '&#224;&#252;&#232;</string></value>
</member>
</struct>
</value>
</fault>
</methodResponse>');
        $m = $this->newMsg('dummy');
        $r = $m->parseResponse($response);
        $v = $r->faultString();
        $this->assertEquals(chr(224) . chr(252) . chr(232) . chr(224) . chr(252) . chr(232), $v);
    }

    public function testValidNumbers()
    {
        $m = $this->newMsg('dummy');
        $fp =
            '<?xml version="1.0"?>
<methodResponse>
<params>
<param>
<value>
<struct>
<member>
<name>integer1</name>
<value><int>01</int></value>
</member>
<member>
<name>float1</name>
<value><double>01.10</double></value>
</member>
<member>
<name>integer2</name>
<value><int>+1</int></value>
</member>
<member>
<name>float2</name>
<value><double>+1.10</double></value>
</member>
<member>
<name>float3</name>
<value><double>-1.10e2</double></value>
</member>
</struct>
</value>
</param>
</params>
</methodResponse>';
        $r = $m->parseResponse($fp);
        $v = $r->value();
        $s = $v->structmem('integer1');
        $t = $v->structmem('float1');
        $u = $v->structmem('integer2');
        $w = $v->structmem('float2');
        $x = $v->structmem('float3');
        $this->assertEquals(1, $s->scalarval());
        $this->assertEquals(1.1, $t->scalarval());
        $this->assertEquals(1, $u->scalarval());
        $this->assertEquals(1.1, $w->scalarval());
        $this->assertEquals(-110.0, $x->scalarval());
    }

    public function testAddScalarToStruct()
    {
        $v = new xmlrpcval(array('a' => 'b'), 'struct');
        // use @ operator in case error_log gets on screen
        $r = @$v->addscalar('c');
        $this->assertEquals(0, $r);
    }

    public function testAddStructToStruct()
    {
        $v = new xmlrpcval(array('a' => new xmlrpcval('b')), 'struct');
        $r = $v->addstruct(array('b' => new xmlrpcval('c')));
        $this->assertEquals(2, $v->structsize());
        $this->assertEquals(1, $r);
        $r = $v->addstruct(array('b' => new xmlrpcval('b')));
        $this->assertEquals(2, $v->structsize());
    }

    public function testAddArrayToArray()
    {
        $v = new xmlrpcval(array(new xmlrpcval('a'), new xmlrpcval('b')), 'array');
        $r = $v->addarray(array(new xmlrpcval('b'), new xmlrpcval('c')));
        $this->assertEquals(4, $v->arraysize());
        $this->assertEquals(1, $r);
    }

    public function testEncodeArray()
    {
        $r = range(1, 100);
        $v = php_xmlrpc_encode($r);
        $this->assertEquals('array', $v->kindof());
    }

    public function testEncodeRecursive()
    {
        $v = php_xmlrpc_encode(php_xmlrpc_encode('a simple string'));
        $this->assertEquals('scalar', $v->kindof());
    }

    public function testBrokenRequests()
    {
        $s = new xmlrpc_server();
        // omitting the 'params' tag: not tolerated by the lib anymore
        $f = '<?xml version="1.0"?>
<methodCall>
<methodName>system.methodHelp</methodName>
<param>
<value><string>system.methodHelp</string></value>
</param>
</methodCall>';
        $r = $s->parserequest($f);
        $this->assertEquals(15, $r->faultCode());
        // omitting a 'param' tag
        $f = '<?xml version="1.0"?>
<methodCall>
<methodName>system.methodHelp</methodName>
<params>
<value><string>system.methodHelp</string></value>
</params>
</methodCall>';
        $r = $s->parserequest($f);
        $this->assertEquals(15, $r->faultCode());
        // omitting a 'value' tag
        $f = '<?xml version="1.0"?>
<methodCall>
<methodName>system.methodHelp</methodName>
<params>
<param><string>system.methodHelp</string></param>
</params>
</methodCall>';
        $r = $s->parserequest($f);
        $this->assertEquals(15, $r->faultCode());
    }

    public function testBrokenResponses()
    {
        $m = $this->newMsg('dummy');
        // omitting the 'params' tag: no more tolerated by the lib...
        $f = '<?xml version="1.0"?>
<methodResponse>
<param>
<value><string>system.methodHelp</string></value>
</param>
</methodResponse>';
        $r = $m->parseResponse($f);
        $this->assertEquals(2, $r->faultCode());
        // omitting the 'param' tag: no more tolerated by the lib...
        $f = '<?xml version="1.0"?>
<methodResponse>
<params>
<value><string>system.methodHelp</string></value>
</params>
</methodResponse>';
        $r = $m->parseResponse($f);
        $this->assertEquals(2, $r->faultCode());
        // omitting a 'value' tag: KO
        $f = '<?xml version="1.0"?>
<methodResponse>
<params>
<param><string>system.methodHelp</string></param>
</params>
</methodResponse>';
        $r = $m->parseResponse($f);
        $this->assertEquals(2, $r->faultCode());
    }

    public function testBuggyHttp()
    {
        $s = $this->newMsg('dummy');
        $f = 'HTTP/1.1 100 Welcome to the jungle

HTTP/1.0 200 OK
X-Content-Marx-Brothers: Harpo
        Chico and Groucho
Content-Length: who knows?



<?xml version="1.0"?>
<!-- First of all, let\'s check out if the lib properly handles a commented </methodResponse> tag... -->
<methodResponse><params><param><value><struct><member><name>userid</name><value>311127</value></member>
<member><name>dateCreated</name><value><dateTime.iso8601>20011126T09:17:52</dateTime.iso8601></value></member><member><name>content</name><value>hello world. 2 newlines follow


and there they were.</value></member><member><name>postid</name><value>7414222</value></member></struct></value></param></params></methodResponse>
<script type="text\javascript">document.write(\'Hello, my name is added nag, I\\\'m happy to serve your content for free\');</script>
 ';
        $r = $s->parseResponse($f);
        $v = $r->value();
        $s = $v->structmem('content');
        $this->assertEquals("hello world. 2 newlines follow\n\n\nand there they were.", $s->scalarval());
    }

    public function testStringBug()
    {
        $s = $this->newMsg('dummy');
        $f = '<?xml version="1.0"?>
<!-- $Id -->
<!-- found by 2z69xks7bpy001@sneakemail.com, amongst others
 covers what happens when there\'s character data after </string>
 and before </value> -->
<methodResponse>
<params>
<param>
<value>
<struct>
<member>
<name>success</name>
<value>
<boolean>1</boolean>
</value>
</member>
<member>
<name>sessionID</name>
<value>
<string>S300510007I</string>
</value>
</member>
</struct>
</value>
</param>
</params>
</methodResponse> ';
        $r = $s->parseResponse($f);
        $v = $r->value();
        $s = $v->structmem('sessionID');
        $this->assertEquals('S300510007I', $s->scalarval());
    }

    public function testWhiteSpace()
    {
        $s = $this->newMsg('dummy');
        $f = '<?xml version="1.0"?><methodResponse><params><param><value><struct><member><name>userid</name><value>311127</value></member>
<member><name>dateCreated</name><value><dateTime.iso8601>20011126T09:17:52</dateTime.iso8601></value></member><member><name>content</name><value>hello world. 2 newlines follow


and there they were.</value></member><member><name>postid</name><value>7414222</value></member></struct></value></param></params></methodResponse>
';
        $r = $s->parseResponse($f);
        $v = $r->value();
        $s = $v->structmem('content');
        $this->assertEquals("hello world. 2 newlines follow\n\n\nand there they were.", $s->scalarval());
    }

    public function testDoubleDataInArrayTag()
    {
        $s = $this->newMsg('dummy');
        $f = '<?xml version="1.0"?><methodResponse><params><param><value><array>
<data></data>
<data></data>
</array></value></param></params></methodResponse>
';
        $r = $s->parseResponse($f);
        $v = $r->faultCode();
        $this->assertEquals(2, $v);
        $f = '<?xml version="1.0"?><methodResponse><params><param><value><array>
<data><value>Hello world</value></data>
<data></data>
</array></value></param></params></methodResponse>
';
        $r = $s->parseResponse($f);
        $v = $r->faultCode();
        $this->assertEquals(2, $v);
    }

    public function testDoubleStuffInValueTag()
    {
        $s = $this->newMsg('dummy');
        $f = '<?xml version="1.0"?><methodResponse><params><param><value>
<string>hello world</string>
<array><data></data></array>
</value></param></params></methodResponse>
';
        $r = $s->parseResponse($f);
        $v = $r->faultCode();
        $this->assertEquals(2, $v);
        $f = '<?xml version="1.0"?><methodResponse><params><param><value>
<string>hello</string>
<string>world</string>
</value></param></params></methodResponse>
';
        $r = $s->parseResponse($f);
        $v = $r->faultCode();
        $this->assertEquals(2, $v);
        $f = '<?xml version="1.0"?><methodResponse><params><param><value>
<string>hello</string>
<struct><member><name>hello><value>world</value></member></struct>
</value></param></params></methodResponse>
';
        $r = $s->parseResponse($f);
        $v = $r->faultCode();
        $this->assertEquals(2, $v);
    }

    public function testAutodecodeResponse()
    {
        $s = $this->newMsg('dummy');
        $f = '<?xml version="1.0"?><methodResponse><params><param><value><struct><member><name>userid</name><value>311127</value></member>
<member><name>dateCreated</name><value><dateTime.iso8601>20011126T09:17:52</dateTime.iso8601></value></member><member><name>content</name><value>hello world. 2 newlines follow


and there they were.</value></member><member><name>postid</name><value>7414222</value></member></struct></value></param></params></methodResponse>
';
        $r = $s->parseResponse($f, true, 'phpvals');
        $v = $r->value();
        $s = $v['content'];
        $this->assertEquals("hello world. 2 newlines follow\n\n\nand there they were.", $s);
    }

    public function testNoDecodeResponse()
    {
        $s = $this->newMsg('dummy');
        $f = '<?xml version="1.0"?><methodResponse><params><param><value><struct><member><name>userid</name><value>311127</value></member>
<member><name>dateCreated</name><value><dateTime.iso8601>20011126T09:17:52</dateTime.iso8601></value></member><member><name>content</name><value>hello world. 2 newlines follow


and there they were.</value></member><member><name>postid</name><value>7414222</value></member></struct></value></param></params></methodResponse>';
        $r = $s->parseResponse($f, true, 'xml');
        $v = $r->value();
        $this->assertEquals($f, $v);
    }

    public function testAutoCoDec()
    {
        $data1 = array(1, 1.0, 'hello world', true, '20051021T23:43:00', -1, 11.0, '~!@#$%^&*()_+|', false, '20051021T23:43:00');
        $data2 = array('zero' => $data1, 'one' => $data1, 'two' => $data1, 'three' => $data1, 'four' => $data1, 'five' => $data1, 'six' => $data1, 'seven' => $data1, 'eight' => $data1, 'nine' => $data1);
        $data = array($data2, $data2, $data2, $data2, $data2, $data2, $data2, $data2, $data2, $data2);
        //$keys = array('zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine');
        $v1 = php_xmlrpc_encode($data, array('auto_dates'));
        $v2 = php_xmlrpc_decode_xml($v1->serialize());
        $this->assertEquals($v1, $v2);
        $r1 = new PhpXmlRpc\Response($v1);
        $r2 = php_xmlrpc_decode_xml($r1->serialize());
        $r2->serialize(); // needed to set internal member payload
        $this->assertEquals($r1, $r2);
        $m1 = new PhpXmlRpc\Request('hello dolly', array($v1));
        $m2 = php_xmlrpc_decode_xml($m1->serialize());
        $m2->serialize(); // needed to set internal member payload
        $this->assertEquals($m1, $m2);
    }

    public function testUTF8Request()
    {
        $sendstring = 'κόσμε'; // Greek word 'kosme'. NB: NOT a valid ISO8859 string!
        $GLOBALS['xmlrpc_internalencoding'] = 'UTF-8';
        \PhpXmlRpc\PhpXmlRpc::importGlobals();
        $f = new xmlrpcval($sendstring, 'string');
        $v = $f->serialize();
        $this->assertEquals("<value><string>&#954;&#8057;&#963;&#956;&#949;</string></value>\n", $v);
        $GLOBALS['xmlrpc_internalencoding'] = 'ISO-8859-1';
        \PhpXmlRpc\PhpXmlRpc::importGlobals();
    }

    public function testUTF8Response()
    {
        $string = chr(224) . chr(252) . chr(232);

        $s = $this->newMsg('dummy');
        $f = "HTTP/1.1 200 OK\r\nContent-type: text/xml; charset=UTF-8\r\n\r\n" . '<?xml version="1.0"?><methodResponse><params><param><value><struct><member><name>userid</name><value>311127</value></member>
<member><name>dateCreated</name><value><dateTime.iso8601>20011126T09:17:52</dateTime.iso8601></value></member><member><name>content</name><value>' . utf8_encode($string) . '</value></member><member><name>postid</name><value>7414222</value></member></struct></value></param></params></methodResponse>
';
        $r = $s->parseResponse($f, false, 'phpvals');
        $v = $r->value();
        $v = $v['content'];
        $this->assertEquals($string, $v);

        $f = '<?xml version="1.0" encoding="UTF-8"?><methodResponse><params><param><value><struct><member><name>userid</name><value>311127</value></member>
<member><name>dateCreated</name><value><dateTime.iso8601>20011126T09:17:52</dateTime.iso8601></value></member><member><name>content</name><value>' . utf8_encode($string) . '</value></member><member><name>postid</name><value>7414222</value></member></struct></value></param></params></methodResponse>
';
        $r = $s->parseResponse($f, false, 'phpvals');
        $v = $r->value();
        $v = $v['content'];
        $this->assertEquals($string, $v);

        $r = php_xmlrpc_decode_xml($f);
        $v = $r->value();
        $v = $v->structmem('content')->scalarval();
        $this->assertEquals($string, $v);
    }

    public function testLatin1Response()
    {
        $string = chr(224) . chr(252) . chr(232);

        $s = $this->newMsg('dummy');
        $f = "HTTP/1.1 200 OK\r\nContent-type: text/xml; charset=ISO-8859-1\r\n\r\n" . '<?xml version="1.0"?><methodResponse><params><param><value><struct><member><name>userid</name><value>311127</value></member>
<member><name>dateCreated</name><value><dateTime.iso8601>20011126T09:17:52</dateTime.iso8601></value></member><member><name>content</name><value>' . $string . '</value></member><member><name>postid</name><value>7414222</value></member></struct></value></param></params></methodResponse>
';
        $r = $s->parseResponse($f, false, 'phpvals');
        $v = $r->value();
        $v = $v['content'];
        $this->assertEquals($string, $v);

        $f = '<?xml version="1.0" encoding="ISO-8859-1"?><methodResponse><params><param><value><struct><member><name>userid</name><value>311127</value></member>
<member><name>dateCreated</name><value><dateTime.iso8601>20011126T09:17:52</dateTime.iso8601></value></member><member><name>content</name><value>' . $string . '</value></member><member><name>postid</name><value>7414222</value></member></struct></value></param></params></methodResponse>
';
        $r = $s->parseResponse($f, false, 'phpvals');
        $v = $r->value();
        $v = $v['content'];
        $this->assertEquals($string, $v);

        $r = php_xmlrpc_decode_xml($f);
        $v = $r->value();
        $v = $v->structmem('content')->scalarval();
        $this->assertEquals($string, $v);
    }

    public function testUTF8IntString()
    {
        $v = new xmlrpcval(100, 'int');
        $s = $v->serialize('UTF-8');
        $this->assertequals("<value><int>100</int></value>\n", $s);
    }

    public function testStringInt()
    {
        $v = new xmlrpcval('hello world', 'int');
        $s = $v->serialize();
        $this->assertequals("<value><int>0</int></value>\n", $s);
    }

    public function testStructMemExists()
    {
        $v = php_xmlrpc_encode(array('hello' => 'world'));
        $b = $v->structmemexists('hello');
        $this->assertequals(true, $b);
        $b = $v->structmemexists('world');
        $this->assertequals(false, $b);
    }

    public function testNilvalue()
    {
        // default case: we do not accept nil values received
        $v = new xmlrpcval('hello', 'null');
        $r = new xmlrpcresp($v);
        $s = $r->serialize();
        $m = $this->newMsg('dummy');
        $r = $m->parseresponse($s);
        $this->assertequals(2, $r->faultCode());
        // enable reception of nil values
        $GLOBALS['xmlrpc_null_extension'] = true;
        \PhpXmlRpc\PhpXmlRpc::importGlobals();
        $r = $m->parseresponse($s);
        $v = $r->value();
        $this->assertequals('null', $v->scalartyp());
        // test with the apache version: EX:NIL
        $GLOBALS['xmlrpc_null_apache_encoding'] = true;
        \PhpXmlRpc\PhpXmlRpc::importGlobals();
        // serialization
        $v = new xmlrpcval('hello', 'null');
        $s = $v->serialize();
        $this->assertequals(1, preg_match('#<value><ex:nil/></value>#', $s));
        // deserialization
        $r = new xmlrpcresp($v);
        $s = $r->serialize();
        $r = $m->parseresponse($s);
        $v = $r->value();
        $this->assertequals('null', $v->scalartyp());
        $GLOBALS['xmlrpc_null_extension'] = false;
        \PhpXmlRpc\PhpXmlRpc::importGlobals();
        $r = $m->parseresponse($s);
        $this->assertequals(2, $r->faultCode());
    }

    public function testLocale()
    {
        $locale = setlocale(LC_NUMERIC, 0);
        /// @todo on php 5.3/win setting locale to german does not seem to set decimal separator to comma...
        if (setlocale(LC_NUMERIC, 'deu', 'de_DE@euro', 'de_DE', 'de', 'ge') !== false) {
            $v = new xmlrpcval(1.1, 'double');
            if (strpos($v->scalarval(), ',') == 1) {
                $r = $v->serialize();
                $this->assertequals(false, strpos($r, ','));
                setlocale(LC_NUMERIC, $locale);
            } else {
                setlocale(LC_NUMERIC, $locale);
                $this->markTestSkipped('did not find a locale which sets decimal separator to comma');
            }
        } else {
            $this->markTestSkipped('did not find a locale which sets decimal separator to comma');
        }
    }

    public function testArrayAccess()
    {
        $v1 = new xmlrpcval(array(new xmlrpcval('one'), new xmlrpcval('two')), 'array');
        $this->assertequals(1, count($v1));
        $out = array('me' => array(), 'mytype' => 2, '_php_class' => null);
        foreach($v1 as $key => $val)
        {
            $expected = each($out);
            $this->assertequals($expected['key'], $key);
            if (gettype($expected['value']) == 'array') {
                $this->assertequals('array', gettype($val));
            } else {
                $this->assertequals($expected['value'], $val);
            }
        }

        $v2 = new \PhpXmlRpc\Value(array(new \PhpXmlRpc\Value('one'), new \PhpXmlRpc\Value('two')), 'array');
        $this->assertequals(2, count($v2));
        $out = array(0 => 'object', 1 => 'object');
        foreach($v2 as $key => $val)
        {
            $expected = each($out);
            $this->assertequals($expected['key'], $key);
            $this->assertequals($expected['value'], gettype($val));
        }
    }
}
