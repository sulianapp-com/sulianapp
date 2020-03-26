<?php

include_once __DIR__ . '/LocalFileTestCase.php';

class DebuggerTest extends PhpXmlRpc_LocalFileTestCase
{
    public function setUp()
    {
        $this->args = argParser::getArgs();

        $this->baseUrl = $this->args['LOCALSERVER'] . str_replace( '/demo/server/server.php', '/debugger/', $this->args['URI'] );

        $this->coverageScriptUrl = 'http://' . $this->args['LOCALSERVER'] . '/' . str_replace( '/demo/server/server.php', 'tests/phpunit_coverage.php', $this->args['URI'] );
    }

    public function testIndex()
    {
        $page = $this->request('index.php');
    }

    public function testController()
    {
        $page = $this->request('controller.php');
    }

    /**
     * @todo test:
     * - list methods
     * - describe a method
     * - execute a method
     * - wrap a method
     */
    public function testAction()
    {
        $page = $this->request('action.php');
    }
}
