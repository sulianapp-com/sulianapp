<?php

namespace Toplan\TaskBalance;        

/**
 * Class Task.
 */
class Task
{
    const RUNNING = 'running';

    const FINISHED = 'finished';

    /**
     * hooks name.
     *
     * @var string[]
     */
    protected static $hooks = [
        'beforeCreateDriver',
        'afterCreateDriver',
        'beforeRun',
        'beforeDriverRun',
        'afterDriverRun',
        'afterRun',
    ];

    /**
     * task name (optional).
     *
     * @var string|null
     */
    protected $name = null;

    /**
     * drivers of task.
     *
     * @var Driver[]
     */
    protected $drivers = [];

    /**
     * backup drivers.
     *
     * @var string[]
     */
    protected $backupDrivers = [];

    /**
     * task status.
     *
     * @var string|null
     */
    protected $status = null;

    /**
     * current driver.
     *
     * @var Driver|null
     */
    protected $currentDriver = null;

    /**
     * task run time.
     *
     * @var array
     */
    protected $time = [
        'started_at'  => 0,
        'finished_at' => 0,
    ];

    /**
     * data of task.
     *
     * @var mixed
     */
    protected $data = null;

    /**
     * logs of drivers.
     *
     * @var array
     */
    protected $results = [];

    /**
     * handlers of hooks.
     *
     * @var array
     */
    protected $handlers = [];

    /**
     * constructor.
     *
     * @param mixed $data
     */
    public function __construct($data = null)
    {
        $this->data($data);
    }

    /**
     * create a new task.
     *
     * @param string|null   $name
     * @param mixed         $data
     * @param \Closure|null $ready
     *
     * @return Task
     */
    public static function create($name = null, $data = null, \Closure $ready = null)
    {
        $task = new self($data);
        $task->name($name);
        if (is_callable($ready)) {
            call_user_func($ready, $task);
        }

        return $task;
    }

    /**
     * run task.
     *
     * @param string|null $driverName
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function run($driverName = null)
    {
        if ($this->isRunning()) {
            return false;
        }
        if (!$this->beforeRun()) {
            return false;
        }
        if (!$driverName) {
            $driverName = $this->getDriverNameByWeight();
        }
        $this->initBackupDrivers([$driverName]);
        $success = $this->runDriver($driverName);

        return $this->afterRun($success);
    }

    /**
     * before run task.
     *
     * @return bool
     */
    protected function beforeRun()
    {
        $this->reset();
        $pass = $this->callHookHandler('beforeRun');
        if ($pass) {
            $this->status = static::RUNNING; 
            $this->time['started_at'] = microtime();
        }

        return $pass;
    }

    /**
     * reset states.
     *
     * @return $this
     */
    protected function reset()
    {
        $this->status = null;
        $this->results = [];
        $this->currentDriver = null;
        $this->time['started_at'] = 0;
        $this->time['finished_at'] = 0;

        return $this;
    }

    /**
     * after run task.
     *
     * @param bool $success
     *
     * @return mixed
     */
    protected function afterRun($success)
    {
        $this->status = static::FINISHED;    
        $this->time['finished_at'] = microtime();  
        $return = [];
        $return['success'] = $success;
        $return['time'] = $this->time;
        $return['logs'] = $this->results;
        $data = $this->callHookHandler('afterRun', $return);

        return is_bool($data) ? $return : $data;
    }

    /**
     * run driver by name.
     *
     * @param string $name
     *
     * @throws TaskBalancerException
     *
     * @return bool
     */
    public function runDriver($name)
    {
        // if not found driver by the name, throw exception.
        $driver = $this->getDriver($name);
        if (!$driver) {
            return false;
        }
        $this->currentDriver = $driver;

        // before run a driver, call 'beforeDriverRun' hooks,
        // and current driver has already changed.
        // If 'beforeDriverRun' hook return false,
        // stop to use current driver and try to use next driver.
        $currentDriverEnable = $this->callHookHandler('beforeDriverRun', $driver);
        if (!$currentDriverEnable) {
            return $this->tryNextDriver();
        }

        // start run driver, and store the result.
        $result = $driver->run();
        $success = $driver->success;
        $data = [
            'driver'  => $driver->name,
            'time'    => $driver->time,
            'success' => $success,
            'result'  => $result,
        ];
        array_push($this->results, $data);

        // call 'afterDriverRun' hooks.
        $this->callHookHandler('afterDriverRun', $data);

        // if failed, try to use next backup driver.
        if (!$success) {
            return $this->tryNextDriver();
        }

        return true;
    }

    /**
     * try to use next backup driver.
     *
     * @return bool
     */
    public function tryNextDriver()
    {
        $backupDriverName = array_pop($this->backupDrivers);
        if ($backupDriverName) {
           return $this->runDriver($backupDriverName);
        }

        return false;
    }

    /**
     * get a driver's name from drivers by driver's weight.
     *
     * @return string|null
     */
    public function getDriverNameByWeight()
    {
        $count = $base = 0;
        $map = [];
        foreach ($this->drivers as $driver) {
            $count += $driver->weight;
            if ($driver->weight) {
                $max = $base + $driver->weight;
                $map[] = [
                    'min'    => $base,
                    'max'    => $max,
                    'driver' => $driver->name,
                ];
                $base = $max;
            }
        }
        if ($count <= 0) {
            return;
        }
        $number = mt_rand(0, $count - 1);
        foreach ($map as $data) {
            if ($number >= $data['min'] && $number < $data['max']) {
                return $data['driver'];
            }
        }
    }

    /**
     * create a new driver instance for current task.
     *
     * @return Driver
     */
    public function driver()
    {
        $args = func_get_args();
        $props = Driver::parseArgs($args);
        $newProps = $this->callHookHandler('beforeCreateDriver', $props);
        if (is_array($newProps)) {
            $props = array_merge($props, $newProps);
        }
        extract($props);
        $driver = Driver::create($this, $name, $weight, $backup, $work);
        $this->drivers[$name] = $driver;
        $this->callHookHandler('afterCreateDriver', $driver);

        return $driver;
    }

    /**
     * has driver.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasDriver($name)
    {
        if (!$this->drivers) {
            return false;
        }

        return isset($this->drivers[$name]);
    }

    /**
     * get a driver by name.
     *
     * @param string $name
     *
     * @return Driver|null
     */
    public function getDriver($name)
    {
        if ($this->hasDriver($name)) {
            return $this->drivers[$name];
        }
    }

    /**
     * remove driver.
     *
     * @param Driver|string $driver
     */
    public function removeDriver($driver)
    {
        if ($driver instanceof Driver) {
            $driver = $driver->name;
        }
        if (!$this->hasDriver($driver)) {
            return;
        }
        $this->removeFromBackupDrivers($driver);
        unset($this->drivers[$driver]);
    }

    /**
     * initialize back up drivers.
     *
     * @param string[] excepted
     */
    public function initBackupDrivers(array $excepted = [])
    {
        $this->backupDrivers = [];
        foreach ($this->drivers as $name => $driver) {
            if ($driver->backup && !in_array($name, $excepted)) {
                array_unshift($this->backupDrivers, $name);
            }
        }
    }

    /**
     * is task running.
     *
     * @return bool
     */
    public function isRunning()
    {
        return $this->status == static::RUNNING;
    }

    /**
     * append driver to backup drivers.
     *
     * @param Driver|string $driver
     */
    public function appendToBackupDrivers($driver)
    {
        if ($driver instanceof Driver) {
            $driver = $driver->name;
        }
        if (!in_array($driver, $this->backupDrivers)) {
            array_push($this->backupDrivers, $driver);
        }
    }

    /**
     * remove driver from backup drivers.
     *
     * @param Driver|string $driver
     */
    public function removeFromBackupDrivers($driver)
    {
        if ($driver instanceof Driver) {
            $driver = $driver->name;
        }
        if (in_array($driver, $this->backupDrivers)) {
            $index = array_search($driver, $this->backupDrivers);
            array_splice($this->backupDrivers, $index, 1);
        }
    }

    /**
     * set the name of task.
     *
     * @param string $name
     *
     * @return $this
     * @throws TaskBalancerException
     */
    public function name($name)
    {
        if (!is_string($name) || empty($name)) {
            throw new TaskBalancerException('Expected task name to be a non-empty string.');
        }
        $this->name = $name;

        return $this;
    }

    /**
     * set the data of task.
     *
     * @param mixed $data
     *
     * @return $this
     */
    public function data($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * set hook's handlers.
     *
     * @param string|array  $hookName
     * @param \Closure|bool $handler
     * @param bool          $override
     *
     * @throws TaskBalancerException
     */
    public function hook($hookName, $handler = null, $override = false)
    {
        if (is_callable($handler) && is_string($hookName)) {
            if (in_array($hookName, self::$hooks)) {
                if (!isset($this->handlers[$hookName])) {
                    $this->handlers[$hookName] = [];
                }
                if ($override) {
                    $this->handlers[$hookName] = [$handler];
                } else {
                    array_push($this->handlers[$hookName], $handler);
                }
            } else {
                throw new TaskBalancerException("Don't support hooks `$hookName`.");
            }
        } elseif (is_array($hookName)) {
            if (is_bool($handler) && $handler) {
                $this->handlers = [];
            }
            foreach ($hookName as $k => $v) {
                $this->hook($k, $v, false);
            }
        }
    }

    /**
     * call hook's handlers.
     *
     * @param string $hookName
     * @param mixed  $data
     *
     * @return mixed
     */
    protected function callHookHandler($hookName, $data = null)
    {
        if (array_key_exists($hookName, $this->handlers)) {
            $handlers = $this->handlers[$hookName] ?: [];
            $result = null;
            foreach ($handlers as $index => $handler) {
                $handlerArgs = $data === null ?
                               [$this, $index, &$handlers, $result] :
                               [$this, $data, $index, &$handlers, $result];
                $result = call_user_func_array($handler, $handlerArgs);
            }
            if ($result === null) {
                return true;
            }

            return $result;
        }

        return true;
    }

    /**
     * properties overload.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
        if (isset($this->drivers[$name])) {
            return $this->drivers[$name];
        }
    }

    /**
     * correct methods 'isset' and 'empty'
     *
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->$name) ?: isset($this->drivers[$name]);
    }

    /**
     * method overload.
     *
     * @param string $name
     * @param array  $args
     *
     * @throws TaskBalancerException
     */
    public function __call($name, $args)
    {
        if (in_array($name, self::$hooks)) {
            if (isset($args[0]) && is_callable($args[0])) {
                $override = isset($args[1]) ? (bool) $args[1] : false;
                $this->hook($name, $args[0], $override);
            }
        } else {
            throw new TaskBalancerException("Not found methods `$name`.");
        }
    }
}
