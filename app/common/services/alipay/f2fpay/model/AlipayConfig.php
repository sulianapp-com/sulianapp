<?php
/**
*
* example目录下为简单的支付样例，仅能用于搭建快速体验微信支付使用
* 样例的作用仅限于指导如何使用sdk，在安全上面仅做了简单处理， 复制使用样例代码时请慎重
* 请勿直接直接使用样例对外提供服务
* 
**/
namespace app\common\services\alipay\f2fpay\model;
use app\common\exceptions\AppException;
use app\common\helpers\Url;
use Yunshop\StoreCashier\store\common\service\RefreshToken;
use Yunshop\StoreCashier\store\models\StoreAlipaySetting;

/**
*
* 该类需要业务自己继承， 该类只是作为deamon使用
* 实际部署时，请务必保管自己的商户密钥，证书等
* 
*/

class AlipayConfig
{
    public $config;
    public $set;
    public function __construct()
    {
        $this->set = $set = \Setting::get('shop.alipay_set');
        $this->config = array (
            //签名方式,默认为RSA2(RSA2048)
            'sign_type' => "RSA2",

            //支付宝公钥
            'alipay_public_key' => $set['alipay_public_key'],

            //商户私钥
            'merchant_private_key' => $set['merchant_private_key'],

            //编码格式
            'charset' => "UTF-8",

            //支付宝网关
            'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

            //应用ID
            'app_id' => $set['app_id'],

            //应用ID
            'pid' => $set['pid'],

            //应用ID
            'name' => $set['name'],

            //异步通知地址,只有扫码支付预下单可用
            'notify_url' => "http://www.baidu.com",

            //最大查询重试次数
            'MaxQueryRetry' => "10",

            //查询间隔
            'QueryDuration' => "3"
        );
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getRoyalty()
    {
        $result = 0;
        if ($this->set['royalty']) {
            $sub_set = StoreAlipaySetting::where('store_id', request()->store_id)->first();
            if ($sub_set->royalty && !$sub_set->no_authorized_royalty) {
                $result = 1;
            }
        }
        return $result;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getAuthToken()
    {
        $app_auth_token = '';
        if (!$this->set['app_type']) {
            $storeAlipaySetting = StoreAlipaySetting::uniacid()->where('store_id', request()->store_id)->first();
            if (!$storeAlipaySetting) {
                throw new AppException('门店未授权支付宝');
            }
            if ($storeAlipaySetting->expires_in < time()) {
                $storeAlipaySetting = RefreshToken::refreshToken();
            }
            $app_auth_token = $storeAlipaySetting->app_auth_token;
        }
        return $app_auth_token;
    }
}
