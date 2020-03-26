<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 24/03/2017
 * Time: 18:48
 */

namespace app\common\listeners;


use app\common\events\WechatMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class WechatMessageListener
{


    public function handle(WechatMessage $event)
    {
        //获取微信对象

        $wechatApp = $event->getWechatApp();
    }
}