<?php
/**
 * 芸众商城模块定义
 *
 * @author YunZhong
 * @url http://www.yunzshop.com/
 */
defined('IN_IA') or exit('Access Denied');

class Yun_shopModule extends WeModule {

	public function settingsDisplay($settings) {
        header('Location:/web/index.php?c=site&a=entry&m=yun_shop&do=setting&route=setting.shop.index');
	}

}