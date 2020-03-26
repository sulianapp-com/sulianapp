Alipay
======

支付宝SDK在Laravel5/Lumen封装包。

该拓展包想要达到在Laravel5/Lumen框架下，便捷使用支付宝的目的。
 
## 使用

要使用支付宝SDK服务提供者，你必须自己注册服务提供者到Laravel/Lumen服务提供者列表中。
基本上有两种方法可以做到这一点。

### Laravel
找到 `config/app.php` 配置文件中，key为 `providers` 的数组，在数组中添加服务提供者。

```php
    'providers' => [
        // ...
        'app\common\component\alipay\AlipayServiceProvider',
    ]
```

运行 `php artisan vendor:publish` 命令，发布配置文件到你的项目中。

### Lumen
在`bootstrap/app.php`里注册服务。

```php
//Register Service Providers
$app->register(app\common\component\alipay\AlipayServiceProvider::class);
```
 

### 说明
配置信息
`Setting::get('alipay')` 为公共配置信息文件，
 ```php
return [
	//合作身份者id，以2088开头的16位纯数字。
	'partner_id' => '2088xxxxxxxxxxxx',
	//卖家支付宝账户。
	'seller_id' => 'xxx@xxx.xxx',
	//pem文件路径
	'pem'=>''
];
``` 
`Setting::get('alipay-web')` 为Web版支付宝SDK配置
```php
return [
	// 安全检验码，以数字和字母组成的32位字符。
	'key' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
	//签名方式
	'sign_type' => 'MD5',
	// 服务器异步通知页面路径。
	'notify_url' => 'http://xxx',
	// 页面跳转同步通知页面路径。
	'return_url' => 'http://xxx'
];
```
 `Setting::get('alipay-mobile')` 为手机端支付宝SDK配置。
 ```php

return [
	// 安全检验码，以数字和字母组成的32位字符。
	'key' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
	// 签名方式
	'sign_type' => 'RSA',
	// 商户私钥。
	'private_key_path' => __DIR__ . '/key/private_key.pem',
	// 阿里公钥。
	'public_key_path' => __DIR__ . '/key/public_key.pem',
	// 异步通知连接。
	'notify_url' => 'http://xxx'
];
```

## 例子

### 支付申请

#### 网页

```php
	// 创建支付单。
	$alipay = app('alipay.web');
	$alipay->setOutTradeNo('order_id');
	$alipay->setTotalFee('order_price');
	$alipay->setSubject('goods_name');
	$alipay->setBody('goods_description');
	
	$alipay->setQrPayMode('4'); //该设置为可选，添加该参数设置，支持二维码支付。

	// 跳转到支付页面。
	return redirect()->to($alipay->getPayLink());
```

#### 手机端

```php
	// 创建支付单。
	$alipay = app('alipay.mobile');
	$alipay->setOutTradeNo('order_id');
	$alipay->setTotalFee('order_price');
	$alipay->setSubject('goods_name');
	$alipay->setBody('goods_description');

	// 返回签名后的支付参数给支付宝移动端的SDK。
	return $alipay->getPayPara();
```

### 结果通知

#### 网页

```php
	/**
	 * 异步通知
	 */
	public function webNotify()
	{
		// 验证请求。
		if (! app('alipay.web')->verify()) {
			Log::notice('Alipay notify post data verification fail.', [
				'data' => Request::instance()->getContent()
			]);
			return 'fail';
		}

		// 判断通知类型。
		switch (Input::get('trade_status')) {
			case 'TRADE_SUCCESS':
			case 'TRADE_FINISHED':
				// TODO: 支付成功，取得订单号进行其它相关操作。
				Log::debug('Alipay notify post data verification success.', [
					'out_trade_no' => Input::get('out_trade_no'),
					'trade_no' => Input::get('trade_no')
				]);
				break;
		}
	
		return 'success';
	}

	/**
	 * 同步通知
	 */
	public function webReturn()
	{
		// 验证请求。
		if (! app('alipay.web')->verify()) {
			Log::notice('Alipay return query data verification fail.', [
				'data' => Request::getQueryString()
			]);
			return view('alipay.fail');
		}

		// 判断通知类型。
		switch (Input::get('trade_status')) {
			case 'TRADE_SUCCESS':
			case 'TRADE_FINISHED':
				// TODO: 支付成功，取得订单号进行其它相关操作。
				Log::debug('Alipay notify get data verification success.', [
					'out_trade_no' => Input::get('out_trade_no'),
					'trade_no' => Input::get('trade_no')
				]);
				break;
		}

		return view('alipay.success');
	}
```

#### 手机端

```php
	/**
	 * 支付宝异步通知
	 */
	public function alipayNotify()
	{
		// 验证请求。
		if (! app('alipay.mobile')->verify()) {
			Log::notice('Alipay notify post data verification fail.', [
				'data' => Request::instance()->getContent()
			]);
			return 'fail';
		}

		// 判断通知类型。
		switch (Input::get('trade_status')) {
			case 'TRADE_SUCCESS':
			case 'TRADE_FINISHED':
				// TODO: 支付成功，取得订单号进行其它相关操作。
				Log::debug('Alipay notify get data verification success.', [
					'out_trade_no' => Input::get('out_trade_no'),
					'trade_no' => Input::get('trade_no')
				]);
				break;
		}

		return 'success';
	}
```