<?php namespace zgldh\UploadManager;

/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 2015/7/23
 * Time: 16:50
 */


use Illuminate\Support\ServiceProvider;

class UploadManagerServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('zgldh\UploadManager\UploadStrategyInterface', \Config::get('upload.upload_strategy'));
        $this->app->singleton('upload-manager', 'zgldh\UploadManager\UploadManager');

        $this->mergeConfigFrom(
            __DIR__ . '/../config/upload.php', 'upload'
        );
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/upload.php'    => config_path('upload.php'),
            __DIR__ . '/../database/migrations/' => database_path('/migrations'),
            __DIR__ . '/../model/Upload.php'     => app_path('Upload.php')
        ]);

        
        $modelClassName = config('upload.upload_model');
        if (class_exists($modelClassName)) {
            $modelClassName::deleted(
                function ($upload) {
                    //
                    $upload->deleteFile(false);
                }
            );
        }
    }
}