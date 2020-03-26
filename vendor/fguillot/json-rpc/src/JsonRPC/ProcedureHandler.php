<?php

namespace JsonRPC;

use BadFunctionCallException;
use Closure;
use InvalidArgumentException;
use ReflectionFunction;
use ReflectionMethod;

/**
 * Class ProcedureHandler
 *
 * @package JsonRPC
 * @author  Frederic Guillot
 */
class ProcedureHandler
{
    /**
     * List of procedures
     *
     * @var array
     */
    protected $callbacks = [];

    /**
     * List of classes
     *
     * @var array
     */
    protected $classes = [];

    /**
     * List of instances
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Before method name to call
     *
     * @var string
     */
    protected $beforeMethodName = '';

    /**
     * Register a new procedure
     *
     * @param  string   $procedure       Procedure name
     * @param  closure  $callback        Callback
     *
     * @return $this
     */
    public function withCallback($procedure, Closure $callback)
    {
        $this->callbacks[$procedure] = $callback;

        return $this;
    }

    /**
     * Bind a procedure to a class
     *
     * @param  string   $procedure    Procedure name
     * @param  mixed    $class        Class name or instance
     * @param  string   $method       Procedure name
     *
     * @return $this
     */
    public function withClassAndMethod($procedure, $class, $method = '')
    {
        if ($method === '') {
            $method = $procedure;
        }

        $this->classes[$procedure] = [$class, $method];

        return $this;
    }

    /**
     * Bind a class instance
     *
     * @param  mixed   $instance
     *
     * @return $this
     */
    public function withObject($instance)
    {
        $this->instances[] = $instance;

        return $this;
    }

    /**
     * Set a before method to call
     *
     * @param  string $methodName
     *
     * @return $this
     */
    public function withBeforeMethod($methodName)
    {
        $this->beforeMethodName = $methodName;

        return $this;
    }

    /**
     * Register multiple procedures from array
     *
     * @param  array  $callbacks Array with procedure names (array keys) and callbacks (array values)
     *
     * @return $this
     */
    public function withCallbackArray($callbacks)
    {
        foreach ($callbacks as $procedure => $callback) {
            $this->withCallback($procedure, $callback);
        }

        return $this;
    }

    /**
     * Bind multiple procedures to classes from array
     *
     * @param  array  $callbacks Array with procedure names (array keys) and class and method names (array values)
     *
     * @return $this
     */
    public function withClassAndMethodArray($callbacks)
    {
        foreach ($callbacks as $procedure => $callback) {
            $this->withClassAndMethod($procedure, $callback[0], $callback[1]);
        }

        return $this;
    }

    /**
     * Execute the procedure
     *
     * @param  string   $procedure    Procedure name
     * @param  array    $params       Procedure params
     *
     * @return mixed
     *
     * @throws \ReflectionException
     */
    public function executeProcedure($procedure, array $params = [])
    {
        if (isset($this->callbacks[$procedure])) {
            return $this->executeCallback($this->callbacks[$procedure], $params);
        } elseif (
            isset($this->classes[$procedure])
            && method_exists($this->classes[$procedure][0], $this->classes[$procedure][1])
        ) {
            return $this->executeMethod($this->classes[$procedure][0], $this->classes[$procedure][1], $params);
        }

        foreach ($this->instances as $instance) {
            if (method_exists($instance, $procedure)) {
                return $this->executeMethod($instance, $procedure, $params);
            }
        }

        throw new BadFunctionCallException('Unable to find the procedure');
    }

    /**
     * Execute a callback
     *
     * @param  Closure   $callback     Callback
     * @param  array     $params       Procedure params
     *
     * @return mixed
     *
     * @throws \ReflectionException
     */
    public function executeCallback(Closure $callback, $params)
    {
        $reflection = new ReflectionFunction($callback);

        $arguments = $this->getArguments(
            $params,
            $reflection->getParameters(),
            $reflection->getNumberOfRequiredParameters(),
            $reflection->getNumberOfParameters()
        );

        return $reflection->invokeArgs($arguments);
    }

    /**
     * Execute a method
     *
     * @param  mixed     $class        Class name or instance
     * @param  string    $method       Method name
     * @param  array     $params       Procedure params
     *
     * @return mixed
     *
     * @throws \ReflectionException
     */
    public function executeMethod($class, $method, $params)
    {
        $instance = is_string($class) ? new $class : $class;
        $reflection = new ReflectionMethod($class, $method);

        $this->executeBeforeMethod($instance, $method);

        $arguments = $this->getArguments(
            $params,
            $reflection->getParameters(),
            $reflection->getNumberOfRequiredParameters(),
            $reflection->getNumberOfParameters()
        );

        return $reflection->invokeArgs($instance, $arguments);
    }

    /**
     * Execute before method if defined
     *
     * @param  mixed  $object
     * @param  string $method
     */
    public function executeBeforeMethod($object, $method)
    {
        if ($this->beforeMethodName !== '' && method_exists($object, $this->beforeMethodName)) {
            call_user_func_array([$object, $this->beforeMethodName], [$method]);
        }
    }

    /**
     * Get procedure arguments
     *
     * @param  array   $requestParams    Incoming arguments
     * @param  array   $methodParams     Procedure arguments
     * @param  integer $nbRequiredParams Number of required parameters
     * @param  integer $nbMaxParams      Maximum number of parameters
     *
     * @return array
     */
    public function getArguments(array $requestParams, array $methodParams, $nbRequiredParams, $nbMaxParams)
    {
        $nbParams = count($requestParams);

        if ($nbParams < $nbRequiredParams) {
            throw new InvalidArgumentException('Wrong number of arguments');
        }

        if ($nbParams > $nbMaxParams) {
            throw new InvalidArgumentException('Too many arguments');
        }

        if ($this->isPositionalArguments($requestParams)) {
            return $requestParams;
        }

        return $this->getNamedArguments($requestParams, $methodParams);
    }

    /**
     * Return true if we have positional parameters
     *
     * @param  array    $request_params      Incoming arguments
     *
     * @return bool
     */
    public function isPositionalArguments(array $request_params)
    {
        return array_keys($request_params) === range(0, count($request_params) - 1);
    }

    /**
     * Get named arguments
     *
     * @param  array $requestParams Incoming arguments
     * @param  array $methodParams  Procedure arguments
     *
     * @return array
     */
    public function getNamedArguments(array $requestParams, array $methodParams)
    {
        $params = [];

        foreach ($methodParams as $p) {
            $name = $p->getName();

            if (array_key_exists($name, $requestParams)) {
                $params[$name] = $requestParams[$name];
            } elseif ($p->isDefaultValueAvailable()) {
                $params[$name] = $p->getDefaultValue();
            } else {
                throw new InvalidArgumentException('Missing argument: '.$name);
            }
        }

        if ($undefinedRequestParams = array_diff_key($requestParams, $params)) {
            throw new InvalidArgumentException(
                'Undefined arguments: '.implode(', ', array_keys($undefinedRequestParams))
            );
        }

        return $params;
    }
}
