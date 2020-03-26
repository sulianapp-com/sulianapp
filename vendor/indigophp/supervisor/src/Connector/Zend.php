<?php

/*
 * This file is part of the Indigo Supervisor package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Supervisor\Connector;

use Indigo\Supervisor\Connector;
use Indigo\Supervisor\Exception\Fault;
use Zend\XmlRpc\Client;
use Zend\XmlRpc\Client\Exception\FaultException;

/**
 * Uses Zend XML-RPC
 *
 * There are known and tested performance issues with it
 *
 * @see https://github.com/lstrojny/fxmlrpc#how-fast-is-it-really
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Zend implements Connector
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @param Client  $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function call($namespace, $method, array $arguments = [])
    {
        try {
            return $this->client->call($namespace.'.'.$method, $arguments);
        } catch (FaultException $e) {
            throw Fault::create($e->getMessage(), $e->getCode());
        }
    }
}
