# Intro

[![Latest Stable Version](https://img.shields.io/packagist/v/toplan/task-balancer.svg)](https://packagist.org/packages/toplan/task-balancer)
[![Total Downloads](https://img.shields.io/packagist/dt/toplan/task-balancer.svg)](https://packagist.org/packages/toplan/task-balancer)

Lightweight and powerful task load balancing.

> like the `nginx` load balancing :smile:

# Features

- Support multiple drives for every task.
- Automatically choose a driver to execute task by drivers' weight value.
- Support multiple backup drivers.
- Task lifecycle and hooks system.

# Install

```php
composer require toplan/task-balancer:~0.5
```

# Usage

```php
//define a task
Balancer::task('task1', function($task){
    //define a driver for current task like this:
    $task->driver('driver_1 100 backup', function ($driver, $data) {
        //do something here
        ...
        //set whether run success/failure at last
        if ($success) {
            $driver->success();
        } else {
            $driver->failure();
        }
        //return some data you need
        return 'some data you need';
    });

    //or like this:
    $task->driver('driver_2', 90, function ($driver, $data) {
        //...same as above..
    })->data(['this is data 2']);

    //or like this:
    $task->driver('driver_3')
    ->weight(0)->backUp()
    ->data(['this is data 3'])
    ->work(function ($driver, $data) {
        //...same as above..
    });
});

//run the task
$result = Balancer::run('task1');
```

The `$result` structure:
```php
[
    'success' => true,
    'time' => [
        'started_at' => timestamp,
        'finished_at' => timestamp
    ],
    'logs' => [
        '0' => [
            'driver' => 'driver_1',
            'success' => false,
            'time' => [
                'started_at' => timestamp,
                'finished_at' => timestamp
            ],
            'result' => 'some data you need'
        ],
        ...
    ]
]
```

# API

## Balancer

### Balancer::task($name[, $data][, Closure $ready]);

Create a task instance, and return it.
The closure `$ready` immediately called with argument `$task`.

```php
Balancer::task('taskName', $data, function($task){
    //task's ready work, such as create drivers.
});
```

> `$data` will store in the task instance.

### Balancer::run($name[, array $options])

Run the task by name, and return the result data.

The keys of `$options`:
- `data`
- `driver`

## Task

### name($name)

set the name of task.

### data($data)

Set the data of task.

### driver($config[, $weight][, 'backup'], Closure $work)

Create a driver for the task. The closure `$work` will been called with arguments `$driver` and `$data`.

> Expected `$weight` to be a integer, default `1`.

```php
$task->driver('driverName 80 backup', function($driver, $data){
    //driver's job content.
});
```

### hasDriver($name)

Whether has the specified driver.

### getDriver($name)

Get driver by name.

### removeDriver($name)

Remove driver from drivers' pool by name.

## Driver

### weight($weight)

Set the weight value of driver.

### backup($is)

Set whether backup driver.

> Expected `$is` to be boolean, default `true`.

### data($data)

Set the data of driver.

> `$data` will store in driver instance.

### work(Closure $work);

Set the job content of driver.

> `$data` equals to `$driver->getData()`

### reset($config[, $weight][, 'backup'], Closure $work)

Reset driver's weight value, job content and reset whether backup.

### destroy()

Remove the driver from task which belongs to.

### failure()

Set the driver running failure.

### success()

Set the driver run successfully.

### getDriverData()

Get the data which store in driver instance.

### getTaskData()

Get the data which store in task instance.


## Lifecycle & Hooks

> Support multiple handlers for every hooks!

### Hooks

| Hook name | handler arguments | influence of the last handler's return value |
| --------- | :----------------: | :-----: |
| beforeCreateDriver | $task, $props, $index, &$handlers, $prevReturn | if an array will been merged into original props |
| afterCreateDriver | $task, $driver, $index, &$handlers, $prevReturn | - |
| beforeRun | $task, $index, &$handlers, $prevReturn | if `false` will stop run task and return `false` |
| beforeDriverRun | $task, $driver, $index, &$handlers, $prevReturn | if `false` will stop to use current driver and try to use next backup driver |
| afterDriverRun | $task, $driverResult, $index, &$handlers, $prevReturn | - |
| afterRun | $task, $taskResult, $index, &$handlers, $prevReturn | if not boolean will override result value |

### Usage

* $task->hook($hookName, $handler, $override)

* $task->beforeCreateDriver($handler, $override)

* $task->afterCreateDriver($handler, $override)

* $task->beforeRun($handler, $override)

* $task->beforeDriverRun($handler, $override)

* $task->afterDriverRun($handler, $override)

* $task->afterRun($handler, $override)

> `$override` default `false`.

```php
//example
$task->beforeRun(function($task, $index, $handlers, $prevReturn){
    //what is $prevReturn?
    echo $prevReturn == null; //true
    //what is $index?
    echo $index == 0; //true
    //what is $handlers?
    echo count($handlers); //2
    //do something..
    return 'beforeRun_1';
}, false);

$task->beforeRun(function($task, $index, $handlers, $prevReturn){
    //what is $prevReturn?
    echo $prevReturn == 'beforeRun_1'; //true
    //what is $index?
    echo $index == 1; //true
    //what is $handlers?
    echo count($handlers); //2
    //do other something..
}, false);
```

# Dependents

- [phpsms](https://github.com/toplan/phpsms)

# License

MIT
