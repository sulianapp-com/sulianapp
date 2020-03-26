<?php

namespace Toplan\TaskBalance; 

/**
 * Class Driver.
 */
class Driver
{
    /**
     * driver name.
     *
     * @var string
     */
    protected $name;

    /**
     * the task belongs to.
     *
     * @var Task
     */
    protected $task;

    /**
     * success.
     *
     * @var bool
     */
    protected $success = false;

    /**
     * weight.
     *
     * @var int
     */
    protected $weight = 1;

    /**
     * is back up driver.
     *
     * @var bool
     */
    protected $backup = false;

    /**
     * driver`s work.
     *
     * @var \Closure
     */
    protected $work = null;

    /**
     * data for run work.
     *
     * @var mixed
     */
    protected $data = null;

    /**
     * run work time.
     *
     * @var array
     */
    protected $time = [
        'started_at'  => 0,
        'finished_at' => 0,
    ];

    /**
     * constructor.
     *
     * @param Task      $task
     * @param string    $name
     * @param int       $weight
     * @param \Closure  $work
     * @param bool      $backup
     *
     * @throws TaskBalancerException
     */
    public function __construct(Task $task, $name, $weight = 1, $backup = false, \Closure $work = null)
    {
        if (!is_string($name) || empty($name)) {
            throw new TaskBalancerException('Expected the driver name to be a non-empty string.');
        }
        if ($task->hasDriver($name)) {
            throw new TaskBalancerException("The driver name `$name` already exits.");
        }
        $weight = intval($weight);

        $this->task = $task;
        $this->name = $name;
        $this->weight = $weight >= 0 ? $weight : 0;
        $this->backup = (bool) $backup;
        $this->work = $work;
    }

    /**
     * create a driver instance.
     *
     * @param Task      $task
     * @param string    $name
     * @param int       $weight
     * @param \Closure  $work
     * @param bool      $backup
     *
     * @return static
     */
    public static function create(Task $task, $name, $weight = 1, $backup = false, \Closure $work = null)
    {
        return new self($task, $name, $weight, $backup, $work);
    }

    /**
     * before run driver work.
     *
     * @return bool
     */
    protected function beforeRun()
    {                        
        $this->time['started_at'] = microtime();

        return true;
    }

    /**
     * run driver`s work.
     *
     * @return mixed
     */
    public function run()
    {
        if (!$this->beforeRun()) {
            return;
        }
        $result = null;
        if (is_callable($this->work)) {
            $result = call_user_func_array($this->work, [$this, $this->getData()]);
        }

        return $this->afterRun($result);
    }

    /**
     * after run driver work.
     *
     * @param mixed $result
     *
     * @return mixed
     */
    protected function afterRun($result)
    {              
        $this->time['finished_at'] = microtime();

        return $result;
    }

    /**
     * set driver run succeed.
     */
    public function success()
    {
        $this->success = true;

        return $this;
    }

    /**
     * set driver run failed.
     */
    public function failure()
    {
        $this->success = false;

        return $this;
    }

    /**
     * set data of driver.
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
     * set weight value of driver.
     *
     * @param int $weight
     *
     * @return $this
     */
    public function weight($weight)
    {
        $this->weight = intval($weight);

        return $this;
    }

    /**
     * set current driver to be a backup driver.
     *
     * @param bool $is
     *
     * @return $this
     */
    public function backup($is = true)
    {
        $is = (bool) $is;
        if ($this->backup === $is) {
            return $this;
        }
        $this->backup = $is;
        if ($this->backup) {
            $this->task->appendToBackupDrivers($this);
        } else {
            $this->task->removeFromBackupDrivers($this);
        }

        return $this;
    }

    /**
     * set the run logic of driver.
     *
     * @param \Closure $work
     *
     * @return $this
     */
    public function work(\Closure $work)
    {
        $this->work = $work;

        return $this;
    }

    /**
     * reset driver's properties.
     */
    public function reset()
    {
        $args = func_get_args();
        extract(self::parseArgs($args));
        if ($this->weight !== $weight) {
            $this->weight($weight);
        }
        if ($this->backup !== $backup) {
            $this->backup($backup);
        }
        if (is_callable($work) && $this->work !== $work) {
            $this->work($work);
        }

        return $this;
    }

    /**
     * get data.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->getDriverData() ?: $this->getTaskData();
    }

    /**
     * get driver data.
     *
     * @return mixed
     */
    public function getDriverData()
    {
        return $this->data;
    }

    /**
     * get task data.
     *
     * @return mixed
     */
    public function getTaskData()
    {
        return $this->task->data;
    }

    /**
     * remove driver from task.
     */
    public function destroy()
    {
        $this->task->removeDriver($this->name);
    }

    /**
     * override.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if ($name === 'data') {
            return $this->getData();
        }
        if (isset($this->$name)) {
            return $this->$name;
        }
    }

    /**
     * parse arguments to driver properties.
     *
     * @param array $args
     *
     * @return array
     */
    public static function parseArgs(array $args)
    {
        $result = [
            'name'   => null,
            'work'   => null,
            'weight' => 1,
            'backup' => false,
        ];
        foreach ($args as $arg) {
            //find work
            if (is_callable($arg)) {
                $result['work'] = $arg;
            }
            //find weight, backup, name
            if (is_string($arg) || is_numeric($arg)) {
                $arg = preg_replace('/\s+/', ' ', "$arg");
                $subArgs = explode(' ', trim($arg));
                foreach ($subArgs as $subArg) {
                    if (preg_match('/^[0-9]+$/', $subArg)) {
                        $result['weight'] = intval($subArg);
                    } elseif (preg_match('/(backup)/', strtolower($subArg))) {
                        $result['backup'] = true;
                    } else {
                        $result['name'] = $subArg;
                    }
                }
            }
        }

        return $result;
    }
}
