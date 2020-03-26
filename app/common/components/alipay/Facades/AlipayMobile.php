<?php

namespace app\common\components\alipay\Facades;

use Illuminate\Support\Facades\Facade;

class AlipayMobile extends Facade
{

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'alipay.mobile';
	}
}