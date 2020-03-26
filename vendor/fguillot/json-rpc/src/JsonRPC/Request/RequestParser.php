<?php

namespace JsonRPC\Request;

use Exception;
use JsonRPC\Exception\InvalidJsonRpcFormatException;
use JsonRPC\MiddlewareHandler;
use JsonRPC\ProcedureHandler;
use JsonRPC\Response\ResponseBuilder;
use JsonRPC\Validator\JsonFormatValidator;
use JsonRPC\Validator\RpcFormatValidator;

/**
 * Class RequestParser
 *
 * @package JsonRPC
 * @author  Frederic Guillot
 */
class RequestParser
{
    /**
     * Request payload
     *
     * @var mixed
     */
    protected $payload;

    /**
     * List of exceptions that should not be relayed to the client
     *
     * @var array
     */
    protected $localExceptions = [
        'JsonRPC\Exception\AuthenticationFailureException',
        'JsonRPC\Exception\AccessDeniedException',
    ];

    /**
     * ProcedureHandler
     *
     * @var ProcedureHandler
     */
    protected $procedureHandler;

    /**
     * MiddlewareHandler
     *
     * @var MiddlewareHandler
     */
    protected $middlewareHandler;

    /**
     * Get new object instance
     *
     * @return RequestParser
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Set payload
     *
     * @param  mixed $payload
     *
     * @return $this
     */
    public function withPayload($payload)
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * Exception classes that should not be relayed to the client
     *
     * @param  mixed $exception
     *
     * @return $this
     */
    public function withLocalException($exception)
    {
        if (is_array($exception)) {
            $this->localExceptions = array_merge($this->localExceptions, $exception);
        } else {
            $this->localExceptions[] = $exception;
        }
        
        return $this;
    }

    /**
     * Set procedure handler
     *
     * @param  ProcedureHandler $procedureHandler
     *
     * @return $this
     */
    public function withProcedureHandler(ProcedureHandler $procedureHandler)
    {
        $this->procedureHandler = $procedureHandler;
        return $this;
    }

    /**
     * Set middleware handler
     *
     * @param  MiddlewareHandler $middlewareHandler
     *
     * @return $this
     */
    public function withMiddlewareHandler(MiddlewareHandler $middlewareHandler)
    {
        $this->middlewareHandler = $middlewareHandler;
        return $this;
    }

    /**
     * Parse incoming request
     *
     * @return string
     *
     * @throws Exception
     */
    public function parse()
    {
        try {

            JsonFormatValidator::validate($this->payload);
            RpcFormatValidator::validate($this->payload);

            $this->middlewareHandler
                ->withProcedure($this->payload['method'])
                ->execute();

            $result = $this->procedureHandler->executeProcedure(
                $this->payload['method'],
                empty($this->payload['params']) ? [] : $this->payload['params']
            );

            if (! $this->isNotification()) {
                return ResponseBuilder::create()
                    ->withId($this->payload['id'])
                    ->withResult($result)
                    ->build();
            }
        } catch (Exception $e) {
            return $this->handleExceptions($e);
        }

        return '';
    }

    /**
     * Handle exceptions
     *
     * @param  Exception $e
     *
     * @return string
     *
     * @throws Exception
     */
    protected function handleExceptions(Exception $e)
    {
        foreach ($this->localExceptions as $exception) {
            if ($e instanceof $exception) {
                throw $e;
            }
        }

        if ($e instanceof InvalidJsonRpcFormatException || ! $this->isNotification()) {
            return ResponseBuilder::create()
                ->withId(isset($this->payload['id']) ? $this->payload['id'] : null)
                ->withException($e)
                ->build();
        }

        return '';
    }

    /**
     * Return true if the message is a notification
     *
     * @return bool
     */
    protected function isNotification()
    {
        return is_array($this->payload) && !isset($this->payload['id']);
    }
}
