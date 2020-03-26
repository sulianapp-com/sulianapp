<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 04/05/2017
 * Time: 00:32
 */

namespace app\common\services;

use app\common\facades\Setting;
use app\common\helpers\Url;
use Illuminate\Support\Facades\Cache;

class Check
{
    public static function app()
    {
        if(app()->environment() !== 'production'){
            return true;
        }
        $key = 'app_auth' . \YunShop::app()->uniacid;
        if(Cache::has($key)){
           if(Cache::get($key)){
               exit(redirect(Url::absoluteWeb('setting.key.index'))->send());
           } else{
               return true;
           }
        }
        $key = Setting::get('shop.key')['key'];
        $secret = Setting::get('shop.key')['secret'];
        $update = new AutoUpdate(null, null, 300);
        $update->setUpdateFile('check_app.json');
        $update->setCurrentVersion(config('version'));
        $update->setUpdateUrl(config('auto-update.checkUrl')); //Replace with your server update directory
        Setting::get('auth.key');
        $update->setBasicAuth($key, $secret);

        if ($update->checkUpdate() === false) {
            Cache::put($key,false,360);
            exit(redirect(Url::absoluteWeb('setting.key.index'))->send());
        }
        Cache::put($key,true,360);
        return true;
    }

    public static function setKey()
    {
        if (app()->environment() == 'production' && strpos(request()->get('route'),'setting.key') !== 0) {
            $key = Setting::get('shop.key')['key'];
            $secret = Setting::get('shop.key')['secret'];

            if (!$key || !$secret) {
                redirect(Url::absoluteWeb('setting.key.index'))->send();
            }
        }
    }

}