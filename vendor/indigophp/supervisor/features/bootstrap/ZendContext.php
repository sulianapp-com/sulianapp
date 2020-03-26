<?php

use Indigo\Supervisor\Connector\Zend;
use Indigo\Supervisor\Supervisor;
use Zend\XmlRpc\Client;
use Zend\Http\Client as HttpClient;

class ZendContext extends FeatureContext
{
    protected function setUpConnector()
    {
        $client = new Client('http://127.0.0.1:9001/RPC2');
        $client->getHttpClient()->setAuth('user', '123', HttpClient::AUTH_BASIC);
        $connector = new Zend($client);
        $this->supervisor = new Supervisor($connector);
    }
}
