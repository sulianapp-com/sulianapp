<?php

namespace spec\Indigo\Supervisor;

use Indigo\Supervisor\Connector;
use Indigo\Supervisor\Process;
use PhpSpec\ObjectBehavior;

class SupervisorSpec extends ObjectBehavior
{
    function let(Connector $connector)
    {
        $this->beConstructedWith($connector);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Indigo\Supervisor\Supervisor');
    }

    function it_checks_connection(Connector $connector)
    {
        $connector->call('system', 'listMethods')->willReturn('response');

        $this->isConnected()->shouldReturn(true);

        $connector->call('system', 'listMethods')->willThrow('Exception');

        $this->isConnected()->shouldReturn(false);
    }

    function it_calls_a_method(Connector $connector)
    {
        $connector->call('namespace', 'method', [])->willReturn('response');

        $this->call('namespace', 'method')->shouldReturn('response');
    }

    function it_checks_if_supervisor_is_running(Connector $connector)
    {
        $connector->call('supervisor', 'getState', [])->willReturn(['statecode' => 1]);

        $this->isRunning()->shouldReturn(true);
    }

    function it_checks_supervisor_state(Connector $connector)
    {
        $connector->call('supervisor', 'getState', [])->willReturn(['statecode' => 1]);

        $this->checkState(1)->shouldReturn(true);
    }

    function it_returns_all_processes(Connector $connector)
    {
        $connector->call('supervisor', 'getAllProcessInfo', [])->willReturn([
            [
                'name' => 'process_name'
            ]
        ]);

        $processes = $this->getAllProcesses();

        $processes->shouldBeArray();
        $processes[0]->shouldHaveType('Indigo\Supervisor\Process');
        $processes[0]->getName()->shouldReturn('process_name');
    }

    function it_returns_a_process_(Connector $connector)
    {
        $connector->call('supervisor', 'getProcessInfo', ['process_name'])->willReturn(['name' => 'process_name']);

        $process = $this->getProcess('process_name');

        $process->shouldHaveType('Indigo\Supervisor\Process');
        $process->getName()->shouldReturn('process_name');
    }
}
