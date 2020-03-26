<?php
/**
 * Created by PhpStorm.
 * User: CHUWU
 * Date: 2019/2/27
 * Time: 13:41
 */
namespace app\common\modules\wechat;

class WechatApplication extends \EasyWeChat\Foundation\Application
{

    public function __construct()
    {
        parent::__construct((new \app\common\modules\wechat\Config())->options);
    }
}