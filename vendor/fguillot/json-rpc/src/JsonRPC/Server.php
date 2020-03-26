<?php

namespace JsonRPC;

use Closure;
use Exception;
use JsonRPC\Request\BatchRequestParser;
use JsonRPC\Request\RequestParser;
use JsonRPC\Response\ResponseBuilder;
use JsonRPC\Validator\HostValidator;
use JsonRPC\Validator\JsonFormatValidator;
use JsonRPC\Validator\UserValidator;

/**
 * JsonRPC server class
 *
 * @package JsonRPC
 * @author  Frederic Guillot
 */
class Server
{
    /**
     * Allowed hosts
     *
     * @var array
     */
    protected $hosts = [];

    /**
     * Data received from the client
     *
     * @var array
     */
    protected $payload = [];

    /**
     * List of exceptions that should not be relayed to the client
     *
     * @var array
     */
    protected $localExceptions = [];

    /**
     * Username
     *
     * @var string
     */
    protected $username = '';

    /**
     * Password
     *
     * @var string
     */
    protected $password = '';

    /**
     * Allowed users
     *
     * @var array
     */
    protected $users = [];

    /**
     * $_SERVER
     *
     * @var array
     */
    protected $serverVariable;

    /**
     * ProcedureHandler object
     *
     * @var ProcedureHandler
     */
    protected $procedureHandler;

    /**
     * MiddlewareHandler object
     *
     * @var MiddlewareHandler
     */
    protected $middlewareHandler;

    /**
     * Response builder
     *
     * @var ResponseBuilder
     */
    protected $responseBuilder;

    /**
     * Response builder
     *
     * @var RequestParser
     */
    protected $requestParser;

    /**
     * Batch request parser
     *
     * @var BatchRequestParser
     */
    protected $batchRequestParser;

    /**
     * @param  string              $request
     * @param  array               $server
     * @param  ResponseBuilder     $responseBuilder
     * @param  RequestParser       $requestParser
     * @param  BatchRequestParser  $batchRequestParser
     * @param  ProcedureHandler    $procedureHandler
     * @param  MiddlewareHandler   $middlewareHandler
     */
    public function __construct(
        $request = '',
        array $server = [],
        ResponseBuilder $responseBuilder = null,
        RequestParser $requestParser = null,
        BatchRequestParser $batchRequestParser = null,
        ProcedureHandler $procedureHandler = null,
        MiddlewareHandler $middlewareHandler = null
    ) {
        if ($request !== '') {
            $this->payload = json_decode($request, true);
        } else {
            $this->payload = json_decode(file_get_contents('php://input'), true);
        }

        $this->serverVariable = $server ?: $_SERVER;
        $this->responseBuilder = $responseBuilder ?: ResponseBuilder::create();
        $this->requestParser = $requestParser ?: RequestParser::create();
        $this->batchRequestParser = $batchRequestParser ?: BatchRequestParser::create();
        $this->procedureHandler = $procedureHandler ?: new ProcedureHandler();
        $this->middlewareHandler = $middlewareHandler ?: new MiddlewareHandler();
    }

    /**
     * Define alternative authentication header
     *
     * @param  string   $header   Header name
     *
     * @return $this
     */
    public function setAuthenticationHeader($header)
    {
        if (! empty($header)) {
            $header = 'HTTP_'.str_replace('-', '_', strtoupper($header));
            $value = $this->getServerVariable($header);

            if (! empty($value)) {
                list($this->username, $this->password) = explode(':', base64_decode($value));
            }
        }

        return $this;
    }

    /**
     * Get ProcedureHandler
     *
     * @return ProcedureHandler
     */
    public function getProcedureHandler()
    {
        return $this->procedureHandler;
    }

    /**
     * Get MiddlewareHandler
     *
     * @return MiddlewareHandler
     */
    public function getMiddlewareHandler()
    {
        return $this->middlewareHandler;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username ?: $this->getServerVariable('PHP_AUTH_USER');
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password ?: $this->getServerVariable('PHP_AUTH_PW');
    }

    /**
     * IP based client restrictions
     *
     * @param  array   $hosts   List of hosts
     *
     * @return $this
     */
    public function allowHosts(array $hosts)
    {
        $this->hosts = $hosts;

        return $this;
    }

    /**
     * HTTP Basic authentication
     *
     * @param  array   $users   Dictionary of username/password
     *
     * @return $this
     */
    public function authentication(array $users)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Register a new procedure
     *
     * @deprecated Use $server->getProcedureHandler()->withCallback($procedure, $callback)
     *
     * @param  string   $procedure       Procedure name
     * @param  closure  $callback        Callback
     *
     * @return $this
     */
    public function register($procedure, Closure $callback)
    {
        $this->procedureHandler->withCallback($procedure, $callback);

        return $this;
    }

    /**
     * Bind a procedure to a class
     *
     * @deprecated Use $server->getProcedureHandler()->withClassAndMethod($procedure, $class, $method);
     *
     * @param  string   $procedure    Procedure name
     * @param  mixed    $class        Class name or instance
     * @param  string   $method       Procedure name
     *
     * @return $this
     */
    public function bind($procedure, $class, $method = '')
    {
        $this->procedureHandler->withClassAndMethod($procedure, $class, $method);

        return $this;
    }

    /**
     * Bind a class instance
     *
     * @deprecated Use $server->getProcedureHandler()->withObject($instance);
     *
     * @param  mixed   $instance    Instance name
     *
     * @return $this
     */
    public function attach($instance)
    {
        $this->procedureHandler->withObject($instance);

        return $this;
    }

    /**
     * Exception classes that should not be relayed to the client
     *
     * @param  Exception|string $exception
     *
     * @return $this
     */
    public function withLocalException($exception)
    {
        $this->localExceptions[] = $exception;

        return $this;
    }

    /**
     * Parse incoming requests
     *
     * @return string
     *
     * @throws Exception
     */
    public function execute()
    {
        try {
            JsonFormatValidator::validate($this->payload);
            HostValidator::validate($this->hosts, $this->getServerVariable('REMOTE_ADDR'));
            UserValidator::validate($this->users, $this->getUsername(), $this->getPassword());

            $this->middlewareHandler
                ->withUsername($this->getUsername())
                ->withPassword($this->getPassword())
            ;

            $response = $this->parseRequest();

        } catch (Exception $e) {
            $response = $this->handleExceptions($e);
        }

        $this->responseBuilder->sendHeaders();

        return $response;
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

        return $this->responseBuilder->withException($e)->build();
    }

    /**
     * Parse incoming request
     *
     * @return string
     *
     * @throws Exception
     */
    protected function parseRequest()
    {
        if (BatchRequestParser::isBatchRequest($this->payload)) {
            return $this->batchRequestParser
                ->withPayload($this->payload)
                ->withProcedureHandler($this->procedureHandler)
                ->withMiddlewareHandler($this->middlewareHandler)
                ->withLocalException($this->localExceptions)
                ->parse();
        }

        return $this->requestParser
            ->withPayload($this->payload)
            ->withProcedureHandler($this->procedureHandler)
            ->withMiddlewareHandler($this->middlewareHandler)
            ->withLocalException($this->localExceptions)
            ->parse();
    }

    /**
     * Check existence and get value of server variable
     *
     * @param  string $variable
     *
     * @return string|null
     */
    protected function getServerVariable($variable)
    {
        return isset($this->serverVariable[$variable]) ? $this->serverVariable[$variable] : null;
    }
}
