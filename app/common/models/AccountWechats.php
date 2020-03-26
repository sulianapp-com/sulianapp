<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/5
 * Time: 上午10:00
 */

namespace app\common\models;

class AccountWechats extends BaseModel
{
    public $table = 'account_wechats';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (config('app.framework') == 'platform') {
            $this->table = 'yz_uniacid_app';
        }
    }

    public static function getAccountByUniacid($uniacid)
    {
        if (!config('app.framework') == 'platform' || file_exists(base_path().'/bootstrap/install.lock')) {
            return self::where('uniacid', $uniacid)->first();
        }
    }

    /**
     * 设置公众号
     * @param $account
     */
    public static function setConfig($account)
    {
        if($account){
            \Config::set('wechat.app_id',$account->key);
            \Config::set('wechat.secret',$account->secret);
            \Config::set('wechat.token',$account->token);
            \Config::set('wechat.aes_key',$account->encodingaeskey);
        }
        return;
    }
}