<?php

namespace JsonRPC;

/**
 * Class MiddlewareHandler
 *
 * @package JsonRPC
 * @author  Frederic Guillot
 */
class MiddlewareHandler
{
    /**
     * Procedure Name
     *
     * @var string
     */
    protected $procedureName = '';

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
     * List of middleware to execute before to call the method
     *
     * @var MiddlewareInterface[]
     */
    protected $middleware = [];

    /**
     * Set username
     *
     * @param  string $username
     *
     * @return $this
     */
    public function withUsername($username)
    {
        if (! empty($username)) {
            $this->username = $username;
        }

        return $this;
    }

    /**
     * Set password
     *
     * @param  string $password
     *
     * @return $this
     */
    public function withPassword($password)
    {
        if (! empty($password)) {
            $this->password = $password;
        }

        return $this;
    }

    /**
     * Set procedure name
     *
     * @param  string $procedureName
     *
     * @return $this
     */
    public function withProcedure($procedureName)
    {
        $this->procedureName = $procedureName;

        return $this;
    }

    /**
     * Add a new middleware
     *
     * @param  MiddlewareInterface $middleware
     *
     * @return MiddlewareHandler
     */
    public function withMiddleware(MiddlewareInterface $middleware)
    {
        $this->middleware[] = $middleware;

        return $this;
    }

    /**
     * Execute all middleware
     */
    public function execute()
    {
        foreach ($this->middleware as $middleware) {
            $middleware->execute($this->username, $this->password, $this->procedureName);
        }
    }
}
