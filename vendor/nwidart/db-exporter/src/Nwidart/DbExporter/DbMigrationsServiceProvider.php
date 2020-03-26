<?php namespace Nwidart\DbExporter;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class DbMigrationsServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('nwidart/db-exporter');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->app['DbMigrations'] = $this->app->share(function()
        {
            $connType = Config::get('database.default');
            $database = Config::get('database.connections.' .$connType );
            return new DbMigrations($database);
        });

        $this->app->booting(function()
            {
                $loader = \Illuminate\Foundation\AliasLoader::getInstance();
                $loader->alias('DbMigrations', 'Nwidart\DbExporter\Facades\DbMigrations');
            });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('DbMigrations');
    }

}