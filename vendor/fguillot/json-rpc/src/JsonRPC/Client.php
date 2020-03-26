<?php

namespace JsonRPC;

use Exception;
use JsonRPC\Request\RequestBuilder;
use JsonRPC\Response\ResponseParser;

/**
 * JsonRPC client class
 *
 * @package JsonRPC
 * @author  Frederic Guillot
 */
class Client
{
    /**
     * If the only argument passed to a function is an array
     * assume it contains named arguments
     *
     * @var boolean
     */
    private $isNamedArguments = true;

    /**
     * Do not immediately throw an exception on error. Return it instead.
     *
     * @var boolean
     */
    private $returnException = false;

    /**
     * True for a batch request
     *
     * @var boolean
     */
    private $isBatch = false;

    /**
     * Batch payload
     *
     * @var array
     */
    private $batch = [];

    /**
     * Http Client
     *
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @param  string      $url               Server URL
     * @param  bool        $returnException   Return exceptions
     * @param  HttpClient  $httpClient        HTTP client object
     */
    public function __construct($url = '', $returnException = false, HttpClient $httpClient = null)
    {
        $this->httpClient = $httpClient ?: new HttpClient($url);
        $this->returnException = $returnException;
    }

    /**
     * Arguments passed are always positional
     *
     * @return $this
     */
    public function withPositionalArguments()
    {
        $this->isNamedArguments = false;

        return $this;
    }

    /**
     * Get HTTP Client
     *
     * @return HttpClient
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Set username and password
     *
     * @param  string $username
     * @param  string $password
     *
     * @return $this
     */
    public function authentication($username, $password)
    {
        $this->httpClient
            ->withUsername($username)
            ->withPassword($password);

        return $this;
    }

    /**
     * Automatic mapping of procedures
     *
     * @param  string   $method   Procedure name
     * @param  array    $params   Procedure arguments
     *
     * @return Exception|Client
     *
     * @throws Exception
     */
    public function __call($method, array $params)
    {
        if ($this->isNamedArguments && count($params) === 1 && is_array($params[0])) {
            $params = $params[0];
        }

        return $this->execute($method, $params);
    }

    /**
     * Start a batch request
     *
     * @return Client
     */
    public function batch()
    {
        $this->isBatch = true;
        $this->batch = [];

        return $this;
    }

    /**
     * Send a batch request
     *
     * @return Exception|Client
     *
     * @throws Exception
     */
    public function send()
    {
        $this->isBatch = false;

        return $this->sendPayload('['.implode(', ', $this->batch).']');
    }

    /**
     * Execute a procedure
     *
     * @param  string      $procedure Procedure name
     * @param  array       $params    Procedure arguments
     * @param  array       $reqattrs
     * @param  string|null $requestId Request Id
     * @param  string[]    $headers   Headers for this request
     *
     * @return $this|Exception|Client
     *
     * @throws Exception
     */
    public function execute($procedure, array $params = [], array $reqattrs = [], $requestId = null, array $headers = [])
    {
        $payload = RequestBuilder::create()
            ->withProcedure($procedure)
            ->withParams($params)
            ->withRequestAttributes($reqattrs)
            ->withId($requestId)
            ->build();

        if ($this->isBatch) {
            $this->batch[] = $payload;

            return $this;
        }

        return $this->sendPayload($payload, $headers);
    }

    /**
     * Send payload
     *
     * @param  string   $payload
     * @param  string[] $headers
     *
     * @return Exception|Client
     *
     * @throws Exception
     */
    private function sendPayload($payload, array $headers = [])
    {
        return ResponseParser::create()
            ->withReturnException($this->returnException)
            ->withPayload($this->httpClient->execute($payload, $headers))
            ->parse();
    }
}
