<?php

namespace JsonRPC\Request;

/**
 * Class RequestBuilder
 *
 * @package JsonRPC\Request
 * @author  Frederic Guillot
 */
class RequestBuilder
{
    /**
     * Request ID
     *
     * @var mixed
     */
    private $id = null;

    /**
     * Method name
     *
     * @var string
     */
    private $procedure = '';

    /**
     * Method arguments
     *
     * @var array
     */
    private $params = [];

    /**
     * Additional request attributes
     *
     * @var array
     */
    private $reqattrs = [];

    /**
     * Get new object instance
     *
     * @return RequestBuilder
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Set id
     *
     * @param  null $id
     *
     * @return RequestBuilder
     */
    public function withId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set method
     *
     * @param  string $procedure
     *
     * @return RequestBuilder
     */
    public function withProcedure($procedure)
    {
        $this->procedure = $procedure;
        return $this;
    }

    /**
     * Set parameters
     *
     * @param  array $params
     *
     * @return RequestBuilder
     */
    public function withParams(array $params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Set additional request attributes
     *
     * @param  array $reqattrs
     *
     * @return RequestBuilder
     */
    public function withRequestAttributes(array $reqattrs)
    {
        $this->reqattrs = $reqattrs;
        return $this;
    }

    /**
     * Build the payload
     *
     * @return string
     */
    public function build()
    {
        $payload = array_merge_recursive($this->reqattrs, [
            'jsonrpc' => '2.0',
            'method' => $this->procedure,
            'id' => $this->id ?: mt_rand(),
        ]);

        if (! empty($this->params)) {
            $payload['params'] = $this->params;
        }

        return json_encode($payload);
    }
}
