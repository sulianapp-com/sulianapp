<?php
namespace app\common\services\wechat\lib;
use app\common\exceptions\AppException;

/**
 * 
 * 微信支付API异常类
 * @author widyhu
 *
 */
class WxPayException extends AppException {
	public function errorMessage()
	{
		return $this->getMessage();
	}
}
