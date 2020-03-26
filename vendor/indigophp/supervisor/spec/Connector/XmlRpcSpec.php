<?php

namespace spec\Indigo\Supervisor\Connector;

use fXmlRpc\ClientInterface;
use fXmlRpc\Exception\ResponseException;
use PhpSpec\ObjectBehavior;

class XmlRpcSpec extends ObjectBehavior
{
    function let(ClientInterface $client)
    {
        $this->beConstructedWith($client);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Indigo\Supervisor\Connector\XmlRpc');
    }

    function it_is_a_conncetor()
    {
        $this->shouldImplement('Indigo\Supervisor\Connector');
    }

    function it_calls_a_method(ClientInterface $client)
    {
        $client->call('namespace.method', [])->willReturn('response');

        $this->call('namespace', 'method')->shouldReturn('response');
    }

    function it_throws_an_exception_when_the_call_fails(ClientInterface $client)
    {
        $e = ResponseException::fault([
            'faultString' => 'Invalid response',
            'faultCode'   => 100,
        ]);

        $client->call('namespace.method', [])->willThrow($e);

        $this->shouldThrow('Indigo\Supervisor\Exception\Fault')->duringCall('namespace', 'method');
    }

    function it_throws_a_known_exception_when_proper_fault_returned(ClientInterface $client)
    {
        $e = ResponseException::fault([
            'faultString' => 'UNKNOWN_METHOD',
            'faultCode'   => 1,
        ]);

        $client->call('namespace.method', [])->willThrow($e);

        $this->shouldThrow('Indigo\Supervisor\Exception\Fault\UnknownMethod')->duringCall('namespace', 'method');
    }
}
