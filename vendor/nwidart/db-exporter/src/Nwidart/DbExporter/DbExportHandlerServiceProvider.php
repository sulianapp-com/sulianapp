<?php namespace Nwidart\DbExporter;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;


class DbExportHandlerServiceProvider extends ServiceProvider
{
    protected $defer = false;

    /**
     * @var DbMigrations $migrator
     */
    protected $migrator;

    /**
     * @var DbSeeding $seeder
     */
    protected $seeder;
    /**
     * @var DbExportHandler $handler
     */
    protected $handler;

    public function boot()
    {
        $this->package('nwidart/db-exporter');
    }

    public function register()
    {
        // Load the classes
        $this->loadClasses();

        // Register the base export handler class
        $this->registerDbExportHandler();

        // Handle the artisan commands
        $this->registerCommands();

        // Load the alias
        $this->loadAlias();
    }

    /**
     * Load to classes
     */
    protected function loadClasses()
    {
        // Instatiate a new DbMigrations class to send to the handler
        $this->migrator = new DbMigrations($this->getDatabaseName());

        // Instatiate a new DbSeeding class to send to the handler
        $this->seeder = new DbSeeding($this->getDatabaseName());

        // Instantiate the handler
        $this->handler = new DbExportHandler($this->migrator, $this->seeder);
    }

    /**
     * Get the database name from the app/config/database.php file
     * @return String
     */
    private function getDatabaseName()
    {
        $connType = Config::get('database.default');
        $database = Config::get('database.connections.' .$connType );

        return $database['database'];
    }

    public function provides()
    {
        return array('DbExportHandler');
    }

    /**
     * Register the needed commands
     */
    public function registerCommands()
    {
        $this->registerMigrationsCommand();
        $this->registerSeedsCommand();
        $this->registerRemoteCommand();
        $this->commands(
            'dbe::migrations',
            'dbe::seeds',
            'dbe::remote'
        );
    }

    /**
     * Register the migrations command
     */
    protected function registerMigrationsCommand()
    {
        $this->app['dbe::migrations'] = $this->app->share(function()
        {
            return new Commands\MigrationsGeneratorCommand($this->handler);
        });
    }

    /**
     * Register the seeds command
     */
    protected function registerSeedsCommand()
    {
        $this->app['dbe::seeds'] = $this->app->share(function()
        {
            return new Commands\SeedGeneratorCommand($this->handler);
        });
    }

    protected function registerRemoteCommand()
    {
        $this->app['dbe::remote'] = $this->app->share(function()
        {
            return new Commands\CopyToRemoteCommand(new Server);
        });
    }

    /**
     * Register the Export handler class
     */
    protected function registerDbExportHandler()
    {
        $this->app['DbExportHandler'] = $this->app->share(function()
        {
            return $this->handler;
        });
    }

    /**
     * Load the alias = One less install step for the user
     */
    protected function loadAlias()
    {
        $this->app->booting(function()
        {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('DbExportHandler', 'Nwidart\DbExporter\Facades\DbExportHandler');
        });
    }

}