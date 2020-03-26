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
 * Process object holding data for a single process
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Process implements \ArrayAccess
{
    /**
     * Process states
     */
    const STOPPED  = 0;
    const STARTING = 10;
    const RUNNING  = 20;
    const BACKOFF  = 30;
    const STOPPING = 40;
    const EXITED   = 100;
    const FATAL    = 200;
    const UNKNOWN  = 1000;

    /**
     * Process info
     *
     * @var array
     */
    protected $payload = [];

    /**
     * @param array $payload
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Returns the process info array
     *
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Returns the process name
     *
     * @return string
     */
    public function getName()
    {
        return $this->payload['name'];
    }

    /**
     * Checks whether the process is running
     *
     * @return boolean
     */
    public function isRunning()
    {
        return $this->checkState(self::RUNNING);
    }

    /**
     * Checks if process is in the given state
     *
     * @param integer $state
     *
     * @return boolean
     */
    public function checkState($state)
    {
        return $this->payload['state'] == $state;
    }

    /**
     * Returns process name
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return isset($this->payload[$offset]) ? $this->payload[$offset] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->payload[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        throw new \LogicException('Process object cannot be altered');
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        throw new \LogicException('Process object cannot be altered');
    }
}
