<?php

$default_host     = '';
$default_port     = '';
$default_db       = '';
$default_username = '';
$default_password = '';
$default_prefix   = '';

$default_slave_db       = '';
$default_slave_host     = '';
$default_slave_username = '';
$default_slave_password = '';
$default_slave_port     = '';

$db_conn_name     = env('DB_CONNECTION', 'mysql');

if (env('APP_Framework',false) != 'platform') {
    include dirname(dirname(dirname(__DIR__))) . '/data/config.php';
} else {
    if (file_exists(base_path('database/config.php'))) {
        include base_path('database/config.php');
    }
}

if (isset($config)) {
    if (isset($config['db']['master'])) {
        $default_host     = $config['db']['master']['host'];
        $default_port     = $config['db']['master']['port'];
        $default_db       = $config['db']['master']['database'];
        $default_username = $config['db']['master']['username'];
        $default_password = $config['db']['master']['password'];
        $default_prefix   = $config['db']['master']['tablepre'];

        $default_slave_db = $config['db']['slave'][1]['database'];
        $default_slave_host     = $config['db']['slave'][1]['host'];
        $default_slave_username = $config['db']['slave'][1]['username'];
        $default_slave_password = $config['db']['slave'][1]['password'];
        $default_slave_port     = $config['db']['slave'][1]['port'];
    } else {
        $default_host     = $config['db']['host'];
        $default_port     = $config['db']['port'];
        $default_db       = $config['db']['database'];
        $default_username = $config['db']['username'];
        $default_password = $config['db']['password'];
        $default_prefix   = $config['db']['tablepre'];
    }
}


return [

    /**
     * 数据查询结果集返回模式
     */
'fetch' => \PDO::FETCH_ASSOC,

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => $db_conn_name,

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

    'sqlite' => [
        'driver' => 'sqlite',
        'database' => env('DB_DATABASE', database_path('database.sqlite')),
        'prefix' => '',
    ],

    'mysql' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST', $default_host),
        'port' => env('DB_PORT', $default_port),
        'database' => env('DB_DATABASE', $default_db),
        'username' => env('DB_USERNAME', $default_username),
        'password' => env('DB_PASSWORD', $default_password),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => env('DB_PREFIX', $default_prefix),
        'strict' => false,
        'engine' => null,
        'loggingQueries'=>true,
        'options'   => [
            \PDO::ATTR_EMULATE_PREPARES => env('DB_PREPARED', false)
        ]

    ],

    'pgsql' => [
        'driver' => 'pgsql',
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '5432'),
        'database' => env('DB_DATABASE', 'forge'),
        'username' => env('DB_USERNAME', 'forge'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'public',
        'sslmode' => 'prefer',
    ],

    'mysql_slave' => [
        'driver' => 'mysql',
        'write' => [
            'host' =>env('DB_HOST', $default_host),
        ],
        'read' => [
            'host'      => env('DB_SLAVE_HOST', $default_slave_host),
        ],
        'port' => env('DB_PORT', $default_port),
        'database' => env('DB_DATABASE', $default_db),
        'username' => env('DB_USERNAME', $default_username),
        'password' => env('DB_PASSWORD', $default_password),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => env('DB_PREFIX', $default_prefix),
        'strict' => false,
        'engine' => null,
        'loggingQueries'=>true,

    ],

],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => 'predis',

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],

];
