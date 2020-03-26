<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 01/03/2017
 * Time: 21:33
 */

namespace app\common\providers;

use app\common\services\Plugin;
use app\common\events;
use Illuminate\Support\Str;
use app\common\services\PluginManager;
use Illuminate\Support\ServiceProvider;

class PluginServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * @var PluginManager $plugins
         */

        if (strpos(request()->path(), 'install')) {
            return;
        }

        // store paths of class files of plugins
        $srcPaths = [];
        $loader = $this->app->make('translation.loader');

        // make view instead of view.finder since the finder is defined as not a singleton
        $finder = $this->app->make('view');

        $this->registerPluginCallbackListener();

        $plugins = app('plugins')->getEnabledPlugins();

        foreach ($plugins as $plugin) {
            // always add paths of translation files for namespace hints
            $loader->addNamespace($plugin->getNameSpace(), $plugin->getPath() . "/lang");

            $srcPaths[$plugin->getNameSpace()] = $plugin->getPath() . "/src";
            // add paths of views
            $finder->addNamespace($plugin->getNameSpace(), $plugin->getPath() . "/views");

        }
        $this->registerClassAutoloader($srcPaths);

        foreach ($plugins as $plugin) {
            $plugin->app()->init();
        }
    }

    protected function registerPluginCallbackListener()
    {
        \Event::listen([
            events\PluginWasEnabled::class,
            events\PluginWasDeleted::class,
            events\PluginWasDisabled::class,
        ], function ($event) {

            // call callback functions of plugin
            if (file_exists($filename = $event->plugin->getPath() . "/callbacks.php")) {
                $callbacks = require $filename;
                $callback = array_get($callbacks, get_class($event));
                return $callback ? app()->call($callback, [$event->plugin]) : null;
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('plugins', PluginManager::class);
    }

    /**
     * Register class autoloader for plugins.
     *
     * @return void
     */
    protected function registerClassAutoloader($paths)
    {
        spl_autoload_register(function ($class) use ($paths) {

            if (!(mb_strpos($class, 'Yunshop') === 0)) {
                return false;
            }
            // traverse in registered plugin paths
            foreach ((array)array_keys($paths) as $namespace) {
                if ($namespace != '' && mb_strpos($class, $namespace) === 0) {
                    // parse real file path
                    $path = $paths[$namespace] . Str::replaceFirst($namespace, '', $class) . ".php";
                    $path = str_replace('\\', '/', $path);
                    if (file_exists($path)) {
                        // include class file if it exists
                        include $path;
                        return true;
                    }
                }
            }

            return false;

        });
    }
}