<?php

namespace app\common\providers;

use App;
use app\common\models\AccountWechats;
use app\common\modules\site\SiteSetting;
use app\common\modules\site\SiteSettingCache;
use app\common\modules\site\WqUniSetting;
use app\common\repositories\OptionRepository;
use app\common\services\mews\captcha\src\Captcha;

use app\framework\Log\CronLog;
use app\framework\Log\TraceLog;
use app\common\facades\Setting;
use app\platform\Repository\SystemSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use app\common\services\Utils;
use Illuminate\Support\Facades\DB;
use SuperClosure\Serializer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Cron::setDisablePreventOverlapping();
        \Cron::setLogger((new CronLog())->getLogger()->getMonolog());
        //微信接口不输出错误
        if (strpos(request()->getRequestUri(), '/api.php') >= 0) {
            error_reporting(0);
            //strpos(request()->get('route'),'setting.key') !== 0 && Check::app();
        }

        $this->globalParamsHandle();

        //设置uniacid
        Setting::$uniqueAccountId = \YunShop::app()->uniacid;
        //设置公众号信息
        AccountWechats::setConfig(AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid));

        //开发模式下记录SQL
        if ($this->app->environment() !== 'production') {
            DB::listen(
                function ($sql) {
                    // $sql is an object with the properties:
                    //  sql: The query
                    //  bindings: the sql query variables
                    //  time: The execution time for the query
                    //  connectionName: The name of the connection

                    // To save  the executed queries to file:
                    // Process the sql and the bindings:
                    foreach ($sql->bindings as $i => $binding) {
                        if ($binding instanceof \DateTime) {
                            $sql->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
                        } else {
                            if (is_string($binding)) {
                                $sql->bindings[$i] = "'$binding'";
                            }
                        }
                    }

                    // Insert bindings into query
                    $query = str_replace(array('%', '?'), array('%%', '%s'), $sql->sql);

                    $query = vsprintf($query, $sql->bindings);

                    // Save the query to file
                    $logFile = fopen(
                        storage_path('logs' . DIRECTORY_SEPARATOR . date('Y-m-d') . '_query.log'),
                        'a+'
                    );
                    //echo storage_path('logs' . DIRECTORY_SEPARATOR . date('Y-m-d') . '_query.log');exit;
                    fwrite($logFile, date('Y-m-d H:i:s') . ': ' . $query . PHP_EOL);
                    fclose($logFile);
                }
            );
        }

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (config('app.env') !== 'production') {
            $this->app->register(\Orangehill\Iseed\IseedServiceProvider::class);
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            $this->app->register(\Way\Generators\GeneratorsServiceProvider::class);
            $this->app->register(\Xethron\MigrationsGenerator\MigrationsGeneratorServiceProvider::class);
        }

        //增加模板扩展tpl
        \View::addExtension('tpl', 'blade');
        //配置表
        $this->app->singleton('options',  OptionRepository::class);
        $this->app->singleton('siteSetting',  SiteSetting::class);
        $this->app->singleton('siteSettingCache',  SiteSettingCache::class);
        $this->app->singleton('SystemSetting', SystemSetting::class);
        $this->app->singleton('WqUniSetting', WqUniSetting::class);
        /**
         * 设置
         */
        App::bind('setting', function()
        {
            return new Setting();
        });

        // Bind captcha
        $this->app->bind('captcha', function($app)
        {
            return new Captcha(
                $app['Illuminate\Filesystem\Filesystem'],
                $app['Illuminate\Config\Repository'],
                $app['Intervention\Image\ImageManager'],
                $app['Illuminate\Hashing\BcryptHasher'],
                $app['Illuminate\Support\Str']
            );
        });
        $this->app->singleton('Log.trace', function (){
            return new TraceLog();
        });

    }

    private function globalParamsHandle()
    {
        if (config('app.framework') == 'platform') {
            global $_W, $_GPC;
            $this->install();

            $uniacid = 0;

            if (!empty(request('uniacid')) && request('uniacid') > 0) {
                $uniacid = request('uniacid');
                Utils::addUniacid();
            }

            if (empty($uniacid) && isset($_COOKIE['uniacid'])) {
                $uniacid = $_COOKIE['uniacid'];
            }

            $cfg = $this->getSiteParams($uniacid);

            $_W   = $cfg;
            $_GPC = array_merge(app('request')->input(), $_COOKIE);
            \config::set('app.global', $cfg);
            \config::set('app.sys_global', array_merge(app('request')->input(), $_COOKIE));
        }
    }

    private function getSiteParams($uniacid)
    {
        $account = AccountWechats::getAccountByUniacid($uniacid);

        $cfg = [
            'uniacid'          => $uniacid,
            'acid'             => $uniacid,
            'account'          => $account ? $account->toArray() : '',
            'openid'           => '',
            'uid'              => \Auth::guard('admin')->user()->uid,
            'username'         => \Auth::guard('admin')->user()->username,
            'siteroot'         => request()->getSchemeAndHttpHost() . '/',
            'siteurl'          => request()->getUri(),
            'attachurl'        => '',
            'attachurl_local'  => request()->getSchemeAndHttpHost() . '/static/upload/',
            'attachurl_remote' => '',
            'charset'          => 'utf-8'
        ];

        return $cfg;
    }

    private function install()
    {
        $path = 'addons/yun_shop';
        $file = $path .  '/api.php';

        if (!file_exists($file)) {
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            $f_data = file_get_contents('api.php');

            file_put_contents($file, $f_data);
        }

        $install = strpos(request()->path(), 'install');
        if (!file_exists(base_path().'/bootstrap/install.lock') && !$install) {
            response()->json([
                'result' => 0,
                'msg' => '',
                'data' => ['status' => -4]
            ], 200, ['charset' => 'utf-8'])
                ->send();
            exit;
        }
    }
}
