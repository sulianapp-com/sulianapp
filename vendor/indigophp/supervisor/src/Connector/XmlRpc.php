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
use fXmlRpc\ClientInterface;
use fXmlRpc\Exception\ResponseException;

/**
 * Basic XML-RPC Connector using fXmlRpc
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class XmlRpc implements Connector
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
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
        } catch (ResponseException $e) {
            throw Fault::create($e->getFaultString(), $e->getFaultCode());
        }
    }
}
