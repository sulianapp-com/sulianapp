<?php

/*
 * This file is part of the Indigo Supervisor package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Supervisor;

/**
 * Supervisor API
 *
 * @method string  getAPIVersion()
 * @method string  getSupervisorVersion()
 * @method string  getIdentification()
 * @method array   getState()
 * @method integer getPID()
 * @method string  readLog(integer $offset, integer $limit)
 * @method boolean clearLog()
 * @method boolean shutdown()
 * @method boolean restart()
 * @method array   getProcessInfo(string $processName)
 * @method array   getAllProcessInfo()
 * @method boolean startProcess(string $name, boolean $wait = true)
 * @method boolean startAllProcesses(boolean $wait = true)
 * @method boolean startProcessGroup(string $name, boolean $wait = true)
 * @method boolean stopProcess(string $name, boolean $wait = true)
 * @method boolean stopAllProcesses(boolean $wait = true)
 * @method boolean stopProcessGroup(string $name, boolean $wait = true)
 * @method boolean sendProcessStdin(string $name, string $chars)
 * @method boolean addProcessGroup(string $name)
 * @method boolean removeProcessGroup(string $name)
 * @method string  readProcessStdoutLog(string $name, integer $offset, integer $limit)
 * @method string  readProcessStderrLog(string $name, integer $offset, integer $limit)
 * @method string  tailProcessStdoutLog(string $name, integer $offset, integer $limit)
 * @method string  tailProcessStderrLog(string $name, integer $offset, integer $limit)
 * @method boolean clearProcessLogs(string $name)
 * @method boolean clearAllProcessLogs()
 *
 * @link http://supervisord.org/api.html
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Supervisor
{
    /**
     * Service states
     */
    const SHUTDOWN   = -1;
    const RESTARTING = 0;
    const RUNNING    = 1;
    const FATAL      = 2;

    /**
     * @var Connector
     */
    protected $connector;

    /**
     * @param Connector $connector
     */
    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }

    /**
     * Checks if a connection is present
     *
     * It is done by sending a bump request to the server and catching any thrown exceptions
     *
     * @return boolean
     */
    public function isConnected()
    {
        try {
            $this->connector->call('system', 'listMethods');
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Calls a method
     *
     * @param string $namespace
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function call($namespace, $method, array $arguments = [])
    {
        return $this->connector->call($namespace, $method, $arguments);
    }

    /**
     * Magic __call method
     *
     * Handles all calls to supervisor namespace
     */
    public function __call($method, $arguments)
    {
        return $this->connector->call('supervisor', $method, $arguments);
    }

    /**
     * Is service running?
     *
     * @return boolean
     */
    public function isRunning()
    {
        return $this->checkState(self::RUNNING);
    }

    /**
     * Checks if supervisord is in given state
     *
     * @param integer $checkState
     *
     * @return boolean
     */
    public function checkState($checkState)
    {
        $state = $this->getState();

        return $state['statecode'] == $checkState;
    }

    /**
     * Returns all processes as Process objects
     *
     * @return array Array of Process objects
     */
    public function getAllProcesses()
    {
        $processes = $this->getAllProcessInfo();

        foreach ($processes as $key => $processInfo) {
            $processes[$key] = new Process($processInfo);
        }

        return $processes;
    }

    /**
     * Returns a specific Process
     *
     * @param string $name Process name or 'group:name'
     *
     * @return Process
     */
    public function getProcess($name)
    {
        $process = $this->getProcessInfo($name);

        return new Process($process);
    }
}
