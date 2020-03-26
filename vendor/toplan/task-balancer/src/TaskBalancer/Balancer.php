<?php

namespace Toplan\TaskBalance;

/**
 * Class Balancer.
 */
class Balancer
{
    /**
     * task instances.
     *
     * @var Task[]
     */
    protected static $tasks = [];

    /**
     * create a task instance.
     *
     * @param string        $name
     * @param mixed         $data
     * @param \Closure|null $ready
     *
     * @return Task
     */
    public static function task($name, $data = null, \Closure $ready = null)
    {
        $task = self::getTask($name);
        if (!$task) {
            if (is_callable($data)) {
                $ready = $data;
                $data = null;
            }
            $task = Task::create($name, $data, $ready);
            self::$tasks[$name] = $task;
        }

        return $task;
    }

    /**
     * run task.
     *
     * @param string $name
     * @param array  $opts
     *
     * @throws TaskBalancerException
     *
     * @return mixed
     */
    public static function run($name, array $opts = [])
    {
        $task = self::getTask($name);
        if (!$task) {
            throw new TaskBalancerException("Not found task `$name`, please define it.");
        }
        if (isset($opts['data'])) {
            $task->data($opts['data']);
        }
        $driverName = isset($opts['driver']) ?: null;

        return $task->run($driverName);
    }

    /**
     * whether has task.
     *
     * @param string $name
     *
     * @return bool
     */
    public static function hasTask($name)
    {
        if (!self::$tasks) {
            return false;
        }
        if (isset(self::$tasks[$name])) {
            return true;
        }

        return false;
    }

    /**
     * get a task instance by name.
     *
     * @param string $name
     *
     * @return Task|null
     */
    public static function getTask($name)
    {
        if (self::hasTask($name)) {
            return self::$tasks[$name];
        }
    }

    /**
     * destroy a task.
     *
     * @param string|string[] $name
     */
    public static function destroy($name)
    {
        if (is_array($name)) {
            foreach ($name as $v) {
                self::destroy($v);
            }
        } elseif (is_string($name) && self::hasTask($name)) {
            unset(self::$tasks[$name]);
        }
    }
}
