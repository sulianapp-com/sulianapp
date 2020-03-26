<?php

include_once __DIR__ . '/LocalFileTestCase.php';

/**
 * Tests for php files in the 'extras' directory
 */
class ExtraTest extends PhpXmlRpc_LocalFileTestCase
{
    public function setUp()
    {
        $this->args = argParser::getArgs();

        $this->baseUrl = $this->args['LOCALSERVER'] . str_replace( '/demo/server/server.php', '/tests/', $this->args['URI'] );

        $this->coverageScriptUrl = 'http://' . $this->args['LOCALSERVER'] . '/' . str_replace( '/demo/server/server.php', 'tests/phpunit_coverage.php', $this->args['URI'] );
    }

    public function testVerifyCompat()
    {
        $page = $this->request('verify_compat.php');
    }
}