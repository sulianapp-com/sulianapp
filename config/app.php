<?php

use app\common\providers\ShopProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    */

    'name' => 'Yunshop',

    /**
     * 微擎模块名
     */
    'module_name' => 'yun_shop',

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'PRC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'zh-CN',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'zh-CN',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY', 'base64:gkli8hs6Q9DbSR/cQw5DNaRBF0jtvf1iGaXc6ja0ZGA='),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log settings for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Settings: "single", "daily", "syslog", "errorlog"
    |
    */

    'log' => env('APP_LOG', 'daily'),

    'log_level' => env('APP_LOG_LEVEL', 'debug'),

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        //Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        //Illuminate\Mail\MailServiceProvider::class,
        //Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        //Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        // 商城管理
        app\common\providers\ShopProvider::class,
        /*
         * Application Service Providers...
         */
        app\common\providers\AppServiceProvider::class,

        app\common\providers\PluginServiceProvider::class,
//         app\common\providers\BroadcastServiceProvider::class,
        app\common\providers\EventServiceProvider::class,
        app\common\providers\RouteServiceProvider::class,

        /**
         * Third-party libraries
         */
        Laracasts\Flash\FlashServiceProvider::class, //提示消息
        Yajra\Datatables\DatatablesServiceProvider::class,
        iscms\Alisms\AlidayuServiceProvider::class,//阿里大鱼
        Maatwebsite\Excel\ExcelServiceProvider::class,//Excel组合
        Overtrue\LaravelWechat\ServiceProvider::class,//微信接口
        app\common\components\alipay\AlipayServiceProvider::class,//支付宝接口
        //表单
        Collective\Html\HtmlServiceProvider::class,
        Watson\BootstrapForm\BootstrapFormServiceProvider::class,
        //表单end
        Ixudra\Curl\CurlServiceProvider::class,

        //二维码
        \app\common\modules\qrCode\QrCodeServiceProvider::class,

        //短信发送
        Toplan\PhpSms\PhpSmsServiceProvider::class,
        Toplan\Sms\SmsManagerServiceProvider::class,
        //计划任务
        Liebig\Cron\Laravel5ServiceProvider::class,
        //上传
        zgldh\UploadManager\UploadManagerServiceProvider::class,

        //拼音
        Overtrue\LaravelPinyin\ServiceProvider::class,
        // 日志浏览
        //Rap2hpoutre\LaravelLogViewer\LaravelLogViewerServiceProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App' => Illuminate\Support\Facades\App::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => \app\framework\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,

        'Setting' => app\common\facades\Setting::class,//设置
        'Option' => app\common\facades\Option::class,
        'Utils' => app\common\services\Utils::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,//Excel 组件

        //表单生成
        'Form' => Collective\Html\FormFacade::class,
        'HTML' => Collective\Html\HtmlFacade::class,
        'BootForm' => Watson\BootstrapForm\Facades\BootstrapForm::class,
        //表单生成end
        'Curl' => Ixudra\Curl\Facades\Curl::class,

        'QrCode' => SimpleSoftwareIO\QrCode\Facades\QrCode::class,
        //php短信发送
        'PhpSms' => Toplan\PhpSms\Facades\Sms::class,
        'SmsManager' => Toplan\Sms\Facades\SmsManager::class,
        //微信接口封装
        'wechat' => Overtrue\LaravelWechat\Facade::class,
        //拼音
        'Pinyin' => Overtrue\LaravelPinyin\Facades\Pinyin::class,
    ],

    'pay_type' => [
        1 => '微信',
        2 => '支付宝',
        3 => '余额'
    ],

    'express' => [
        'KDN' => [
            'reqURL'=>'http://api.kdniao.com/api/dist'
        ]
    ],

    'isWeb'  => env('IS_WEB', '/web/index.php'),
    'webPath' => env('ROOT_PATH', '/addons/yun_shop'),
    'extendDir' => env('EXTEND_DIR', 'addons'),
    'global' => [],
    'sys_global' => [],
    'framework' => env('APP_Framework', false),
    'APP_Framework' => env('APP_Framework', false),
    'PLUGIN_MARKET_SOURCE' => env('PLUGIN_MARKET_SOURCE'),
];
