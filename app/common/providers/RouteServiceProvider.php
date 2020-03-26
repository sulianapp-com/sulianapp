<?php

namespace app\common\providers;

use app\common\services\Check;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'app';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        if (config('app.framework') == 'platform') {
            $this->mapWebBootRoutes();
            $this->mapPlatformRoutes();
            $this->mapShopRoutes();
            $this->mapApiRoutes();
        } else {
            $this->mapWebRoutes();
        }
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => ['web'],
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/web.php');
        });
    }

    protected function mapWebBootRoutes()
    {
        Route::group([
            'prefix' => 'api',
            'middleware' => ['web'],
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/boot.php');
        });
    }

    /**
     * 前端路由
     *
     */
    protected function mapApiRoutes()
    {
        Route::group([
            'middleware' => ['web'],
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/api.php');
        });
    }

    /**
     * 框架路由
     *
     */
    protected function mapPlatformRoutes()
    {
        Route::group([
            'prefix' => 'admin',
            'middleware' => ['admin'],
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/admin.php');
        });
    }

    /**
     * 商城路由
     *
     */
    protected function mapShopRoutes()
    {
        Route::group([
            'prefix' => 'admin',
            'middleware' => ['admin'],
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/shop.php');
        });
    }

}
