<?php
namespace app\common\components\alipay;

use Illuminate\Support\ServiceProvider;
use Setting;

class AlipayLumenServiceProvider extends ServiceProvider
{

	/**
	 * boot process
	 */
	public function boot()
	{
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{

		$this->app->bind('alipay.mobile', function ($app)
		{
			$alipay = new Mobile\SdkPayment();

			$alipay->setPartner(Setting::get('alipay.partner_id'))
				->setSellerId(Setting::get('alipay.seller_id'))
				->setSignType(Setting::get('alipay-mobile.sign_type'))
				->setPrivateKeyPath(Setting::get('alipay-mobile.private_key_path'))
				->setPublicKeyPath(Setting::get('alipay-mobile.public_key_path'))
				->setNotifyUrl(Setting::get('alipay-mobile.notify_url'));

			return $alipay;
		});

		$this->app->bind('alipay.web', function ($app)
		{
			$alipay = new Web\SdkPayment();

			$alipay->setPartner(Setting::get('alipay.partner_id'))
				->setSellerId(Setting::get('alipay.seller_id'))
				->setKey(Setting::get('alipay-web.key'))
				->setSignType(Setting::get('alipay-web.sign_type'))
				->setNotifyUrl(Setting::get('alipay-web.notify_url'))
				->setReturnUrl(Setting::get('alipay-web.return_url'))
				->setExterInvokeIp($app->request->getClientIp());

			return $alipay;
		});

		$this->app->bind('alipay.wap', function ($app)
		{
			$alipay = new Wap\SdkPayment();

			$alipay->setPartner(Setting::get('alipay.partner_id'))
			->setSellerId(Setting::get('alipay.seller_id'))
			->setKey(Setting::get('alipay-web.key'))
			->setSignType(Setting::get('alipay-web.sign_type'))
			->setNotifyUrl(Setting::get('alipay-web.notify_url'))
			->setReturnUrl(Setting::get('alipay-web.return_url'))
			->setExterInvokeIp($app->request->getClientIp());

			return $alipay;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [
			'alipay.mobile',
			'alipay.web',
			'alipay.wap',
		];
	}
}
