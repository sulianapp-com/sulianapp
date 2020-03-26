<?php
/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new \app\framework\Foundation\Application(
    realpath(__DIR__.'/../')
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/
$app->singleton('Log.trace', function (){
    return new \app\framework\Log\TraceLog();
});
$app->singleton('Log.debug', function (){
    return new \app\framework\Log\DebugLog();
});
$app->singleton('Log.error', function (){
    return new \app\framework\Log\ErrorLog();
});
$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    app\Kernel::class
);
$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    app\console\Kernel::class
);
$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    app\common\exceptions\Handler::class
);
error_reporting(E_ALL);ini_set('display_errors', 1);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/
return $app;