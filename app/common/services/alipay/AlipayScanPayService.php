<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/10/11
 * Time: 15:14
 */

namespace app\common\services\alipay;


use app\common\exceptions\AppException;
use app\common\services\alipay\f2fpay\model\AlipayConfig;
use app\common\services\alipay\f2fpay\model\builder\AlipayTradePayContentBuilder;
use app\common\services\alipay\f2fpay\model\builder\ExtendParams;
use app\common\services\alipay\f2fpay\model\builder\GoodsDetail;
use app\common\services\alipay\f2fpay\service\AlipayTradeService;
use app\common\services\Pay;
use Yunshop\StoreCashier\store\common\service\RefreshToken;
use Yunshop\StoreCashier\store\models\StoreAlipaySetting;

class AlipayScanPayService extends Pay
{
    public $set;
    public function __construct()
    {
        $this->set = $set = \Setting::get('shop.alipay_set');
    }


    /**
     * 订单支付/充值
     * @param array $data
     * @return mixed
     * @throws AppException
     * @throws \Exception
     */
    function doPay($data = [])
    {
        if (\YunShop::request()->type != 9) {
            throw new AppException('不是商家APP 支付宝扫码支付不可用');
        }

        if ($data['pay_type'] == 'alipay') {
            $third_type = '支付宝扫码支付';
        } else {
            $third_type = '支付宝人脸支付';
        }
        $op = '微信扫码支付 订单号：' . $data['order_no'];
        $pay_order_model = $this->log(1, $third_type, $data['amount'], $op, $data['order_no'], Pay::ORDER_STATUS_NON, $this->getMemberId());


        // (必填) 商户网站订单系统中唯一订单号，64个字符以内，只能包含字母、数字、下划线，
        $outTradeNo = $data['pay_sn'];

        // (必填) 订单标题，粗略描述用户的支付目的。如“XX品牌XXX门店消费”
        $subject = $data['subject'];

        // (必填) 订单总金额，单位为元，不能超过1亿元
        $totalAmount = $data['amount'];

        // (必填) 付款条码，用户支付宝钱包手机app点击“付款”产生的付款条码
        $authCode = $data['auth_code']; //28开头18位数字
//        $authCode = "283797041311679095"; //28开头18位数字

        // 订单描述，可以对交易或商品进行一个详细地描述，比如填写"购买商品2件共15.00元"
        $body = $data['body'];

        //商户操作员编号，添加此参数可以为商户操作员做销售统计
//        $operatorId = "test_operator_id";

        // (可选) 商户门店编号，通过门店号和商家后台可以配置精准到门店的折扣信息，详询支付宝技术支持
//        $storeId = "test_store_id";

        // 支付宝的店铺编号
//        $alipayStoreId = "2088102175624020";

        // 业务扩展参数，目前可添加由支付宝分配的系统商编号(通过setSysServiceProviderId方法)，详情请咨询支付宝技术支持
//        $providerId = ""; //系统商pid,作为系统商返佣数据提取的依据
//        $extendParams = new ExtendParams();
//        $extendParams->setSysServiceProviderId($providerId);
//        $extendParamsArr = $extendParams->getExtendParams();

        // 支付超时，线下扫码交易定义为5分钟
        $timeExpress = "5m";

        // 商品明细列表，需填写购买商品详细信息，
//        $goodsDetailList = [
//            [
//                'goods_id' => $data['goods_id'],
//                'goods_name' => $data['goods_name'],
//                'price' => $data['price'],
//                'quantity' => $data['total'],
//            ]
//        ];
        // 创建请求builder，设置请求参数
        $barPayRequestBuilder = new AlipayTradePayContentBuilder();

        $appAuthToken = '';
        $pid = '';
        if (!$this->set['app_type']) {

            //第三方应用授权令牌,商户授权系统商开发模式下使用
            $appAuthToken = $this->getAuthToken();//根据真实值填写
            $pid = $this->set['pid'];//分佣
            $barPayRequestBuilder->setSysServiceProviderId($pid);
        }

        $barPayRequestBuilder->setOutTradeNo($outTradeNo);
        $barPayRequestBuilder->setTotalAmount($totalAmount);
        $barPayRequestBuilder->setAuthCode($authCode);
        $barPayRequestBuilder->setTimeExpress($timeExpress);
        $barPayRequestBuilder->setSubject($subject);
        $barPayRequestBuilder->setBody($body);
        $barPayRequestBuilder->setAppAuthToken($appAuthToken);
        
//        $barPayRequestBuilder->setExtendParams($extendParamsArr);
//        $barPayRequestBuilder->setGoodsDetailList($goodsDetailList);
//        $barPayRequestBuilder->setStoreId($storeId);
//        $barPayRequestBuilder->setOperatorId($operatorId);
//        $barPayRequestBuilder->setAlipayStoreId($alipayStoreId);



        $config = new AlipayConfig();
        // 调用barPay方法获取当面付应答
        $barPay = new AlipayTradeService($config->getConfig());
        $barPayResult = $barPay->barPay($barPayRequestBuilder);
        if ($barPayResult->getTradeStatus() == "FAILED") {
            throw new AppException('支付宝支付失败!!!');
        }

        if ($barPayResult->getTradeStatus() == "UNKNOWN") {
            throw new AppException('系统异常，订单状态未知!!!');
        }


        //设置支付参数


        //请求数据日志
        self::payRequestDataLog($data['order_no'], $pay_order_model->type,
            $pay_order_model->third_type, json_encode($barPayResult->getResponse()));

        $result = (array)$barPayResult->getResponse();
        $result['royalty'] = $config->getRoyalty();

        return $result;
    }

    public function getMemberId()
    {
        return \YunShop::app()->getMemberId() ? : 0;
    }

    /**
     * 退款
     *
     * @param $out_trade_no 订单号
     * @param $totalmoney 订单总金额
     * @param $refundmoney 退款金额
     * @return mixed
     */
    function doRefund($out_trade_no, $totalmoney, $refundmoney)
    {
        // TODO: Implement doRefund() method.
    }

    /**
     * 提现
     *
     * @param $member_id 提现者用户ID
     * @param $out_trade_no 提现批次单号
     * @param $money 提现金额
     * @param $desc 提现说明
     * @param $type 只针对微信 1-企业支付(钱包) 2-红包
     * @return mixed
     */
    function doWithdraw($member_id, $out_trade_no, $money, $desc, $type)
    {
        // TODO: Implement doWithdraw() method.
    }

    /**
     * 构造签名
     *
     * @return mixed
     */
    function buildRequestSign()
    {
        // TODO: Implement buildRequestSign() method.
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getAuthToken()
    {
        $storeAlipaySetting = StoreAlipaySetting::uniacid()->where('store_id', request()->store_id)->first();
        if (!$storeAlipaySetting) {
            throw new AppException('门店未授权支付宝');
        }
        if ($storeAlipaySetting->expires_in < time()) {
            $storeAlipaySetting = RefreshToken::refreshToken();
        }
        $app_auth_token = $storeAlipaySetting->app_auth_token;
        return $app_auth_token;
    }
}