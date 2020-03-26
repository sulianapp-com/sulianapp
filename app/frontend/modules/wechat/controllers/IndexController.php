<?php

namespace app\frontend\modules\wechat\controllers;

use app\common\components\BaseController;
use app\common\models\AccountWechats;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/3
 * Time: 22:16
 */
class IndexController extends BaseController
{
    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        $uniacid = request('id');

        //设置uniacid
        \config::set('app.global', array_merge(\config::get('app.global'), ['uniacid' => $uniacid]));
        \YunShop::app()->uniacid = $uniacid;
        \Setting::$uniqueAccountId = $uniacid;
        //设置公众号信息
        AccountWechats::setConfig(AccountWechats::getAccountByUniacid($uniacid));
    }

    public function index()
    {
        \Log::debug('----------公众号消息---------',$_GET);
        // 接入判断
        if ( isset( $_GET["signature"] ) && isset( $_GET["timestamp"] ) && isset( $_GET["nonce"] ) && isset( $_GET["echostr"] ) ) {
            $signature = $_GET["signature"];
            $timestamp = $_GET["timestamp"];
            $nonce     = $_GET["nonce"];
            $token = \Setting::get('plugin.wechat.token');
            $tmpArr    = [ $token, $timestamp, $nonce ];
            sort( $tmpArr, SORT_STRING );
            $tmpStr = implode( $tmpArr );
            $tmpStr = sha1( $tmpStr );
            if ( $tmpStr == $signature ) {
                \Log::debug('----------公众号接入成功---------',$_GET);
                \Setting::set('plugin.wechat.status', 1);
                \Log::debug('----------公众号接入成功状态---------',\Setting::get('plugin.wechat.status'));
                ob_clean();
                return $_GET["echostr"];
            } else {
                \Log::debug('----------公众号接入失败---------',$_GET);
            }
        } else {// 不是接入，则触发事件，交给监听者处理.
            // 获取第三方库easyWechat的app对象
            $wechatApp = new \app\common\modules\wechat\WechatApplication();
            $server = $wechatApp->server;
            try {
                $message = $server->getMessage();// 异常代码
                if (\Setting::get('plugin.wechat.is_open')) {//公众号开启，才进行事件触发
                    event(new \app\common\events\WechatMessage($wechatApp,$server,$message));
                    if($message['Event']=="subscribe" && app('plugins')->isEnabled('pet')){
                        event(new \app\common\events\PetWeChatEvent($_GET));
                    }
                }

            } catch (\Exception $exception) {
                \Log::debug('----------公众号异常---------',$exception->getMessage());
            }
        }
    }
}