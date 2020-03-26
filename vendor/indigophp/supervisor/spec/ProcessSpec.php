<?php

namespace spec\Indigo\Supervisor;

use Indigo\Supervisor\Connector;
use PhpSpec\ObjectBehavior;

class ProcessSpec extends ObjectBehavior
{
    protected $process = [
        'name'           => 'process name',
        'group'          => 'group name',
        'start'          => 1200361776,
        'stop'           => 0,
        'now'            => 1200361812,
        'state'          => 20,
        'statename'      => 'RUNNING',
        'spawnerr'       => '',
        'exitstatus'     => 0,
        'stdout_logfile' => '/path/to/stdout-log',
        'stderr_logfile' => '/path/to/stderr-log',
        'pid'            => 1,
    ];

    function let(Connector $connector)
    {
        $this->beConstructedWith($this->process, $connector);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Indigo\Supervisor\Process');
    }

    function it_has_payload()
    {
        $this->getPayload()->shouldReturn($this->process);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn($this->process['name']);
        $this->offsetExists('name')->shouldReturn(true);
        $this->offsetGet('name')->shouldReturn($this->process['name']);
        $this->__toString()->shouldReturn($this->process['name']);
    }

    function it_checks_if_process_is_running()
    {
        $this->isRunning()->shouldReturn(true);
    }

    function it_checks_process_state()
    {
        $this->checkState(20)->shouldReturn(true);
        $this->checkState(2)->shouldReturn(false);
    }

    function it_throws_an_exception_when_being_altered_by_calling_offset_set()
    {
        $this->shouldThrow('LogicException')->duringOffsetSet('key', 'value');
    }

    function it_throws_an_exception_when_being_altered_by_calling_offset_unset()
    {
        $this->shouldThrow('LogicException')->duringOffsetUnset('key', 'value');
    }
}
