<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/7/10
 * Time: 下午2:31
 */

namespace app\framework\Foundation\Bootstrap;


use app\framework\Http\Request;
use Illuminate\Foundation\Application;

class SetRequestForConsole
{
    /**
     * @param Application $app
     */
    public function bootstrap(Application $app)
    {
        $url = $app->make('config')->get('app.url', 'http://localhost');

        $app->instance('request', Request::create($url, 'GET', [], [], [], $_SERVER));
    }
}