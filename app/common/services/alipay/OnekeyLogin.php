<?php
namespace app\common\services\alipay;

/**
* create 2018/6/7 15:31
*/
class OnekeyLogin
{
	
	public function __construct()
	{
		# code...
	}

	//支付宝用户是否根据手机号同步数据
    public static function alipayPluginMobileState()
    {
      if (app('plugins')->isEnabled('alipay-onekey-login')) {
          $alipay_set = \Setting::get('plugin.alipay_onekey_login');

          return $alipay_set['bind_mobile'];
      }

      return 0;
    }
}