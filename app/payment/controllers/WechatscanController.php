<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/4/24
 * Time: 下午3:10
 */

namespace app\payment\controllers;

use app\common\helpers\Url;
use app\common\models\AccountWechats;
use app\common\modules\orderGoods\OrderGoodsCollection;
use app\common\services\Pay;
use app\common\services\wechat\lib\WxPayConfig;
use app\common\services\wechat\lib\WxPayResults;
use app\payment\PaymentController;
use Yunshop\ConvergePay\services\NotifyService;
use app\common\events\withdraw\WithdrawSuccessEvent;
use Yunshop\StoreCashier\frontend\store\models\PreOrder;
use Yunshop\StoreCashier\frontend\store\models\PreOrderGoods;

class WechatscanController extends PaymentController
{
    private $attach = [];
    private $parameter = [];


    public function preAction()
    {
        parent::preAction();

        if (empty(\YunShop::app()->uniacid)) {
            $post = $this->getResponseResult();
            if (\YunShop::request()->attach) {
                \Setting::$uniqueAccountId = \YunShop::app()->uniacid = \YunShop::request()->attach['uniacid'];
            } else {
//                $this->attach = explode(':', $post['attach']);
                $this->attach = $post['attach'];
                \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $this->attach['uniacid'];
            }
        }
    }

    /**
     * @throws \app\common\exceptions\AppException
     * @throws \app\common\exceptions\ShopException
     * @throws \app\common\services\wechat\lib\WxPayException
     */
    public function notifyUrl()
    {
        $post = $this->getResponseResult();
        \Log::info('微信支付回调结果', $post);
        $this->log($post);

        $xml = file_get_contents('php://input');
        $config = new WxPayConfig();
        $result = WxPayResults::Init($config, $xml);
        if (!$result) {
            echo "fail";
            exit();
        }

//        $result = $this->createOrder($post);

        if ($result) {
            $data = [
                'total_fee'    => $post['total_fee'] ,
                'out_trade_no' => $post['out_trade_no'],
                'trade_no'     => $post['transaction_id'],
                'unit'         => 'fen',
                'pay_type'     => '微信扫码支付',
                'pay_type_id'     => $post['trade_type'] == 'MICROPAY' ? 38 : 0
            ];

            $this->payResutl($data);
            echo "success";
        } else {
            echo "fail";
        }
    }


    /**
     * 支付日志
     *
     * @param $post
     */
    public function log($post)
    {
        //访问记录
        Pay::payAccessLog();
        //保存响应数据
        Pay::payResponseDataLog($post['out_trade_no'], '微信扫码支付', json_encode($post));
    }

    /**
     * @param $post
     * @return bool|PreOrder
     * @throws \app\common\exceptions\AppException
     * @throws \app\common\exceptions\ShopException
     */
    public function createOrder($post)
    {

        if ($post['openid']) {
            //注册会员
            $member = 0;
        }

        if (!$this->attach) {
            return false;
        }

        \app\frontend\models\Member::$current = $member;

        $orderGoods = new PreOrderGoods([
            'total' => 1,
            'option_id' => 1,
            'goods_id' => $post['goods_id'],
        ]);
        $orderGoodsCollection[] = $orderGoods;

        $orderGoodsCollection = new OrderGoodsCollection($orderGoodsCollection);
        $preOrder = new PreOrder();

        $preOrder->init($member, $orderGoodsCollection, $post['attach']);

        $preOrder->generate();

        \app\frontend\models\Member::$current = null;

        return $preOrder;
    }


    /**
     * 签名验证
     *
     * @return bool
     */
    public function getSignResult($post)
    {
        switch ($post['trade_type']) {
            case 'JSAPI':
                $pay = \Setting::get('shop.pay');

                if (isset($this->attach[1]) && $this->attach[1] == 'wechat') {
                    $min_set = \Setting::get('plugin.min_app');

                    $pay = [
                        'weixin_appid' => $min_set['key'],
                        'weixin_secret' => $min_set['secret'],
                        'weixin_mchid' => $min_set['mchid'],
                        'weixin_apisecret' => $min_set['api_secret'],
                        'weixin_cert'   => '',
                        'weixin_key'    => ''
                    ];
                }

                break;
            case 'APP' :
                $pay = \Setting::get('shop_app.pay');
                break;
        }

        $app = $this->getEasyWeChatApp($pay);
        $payment = $app->payment;
        $notify = $payment->getNotify();

        //老版本-无参数
        $valid = $notify->isValid();

        if (!$valid) {
            //新版本-有参数
            $valid = $notify->isValid($pay['weixin_apisecret']);
        }

        return $valid;
    }

    /**
     * 获取回调结果
     *
     * @return array|mixed|\stdClass
     */
    public function getResponseResult()
    {
        $input = file_get_contents('php://input');
        if (!empty($input) && empty($_POST['out_trade_no'])) {
            //禁止引用外部xml实体
            $disableEntities = libxml_disable_entity_loader(true);

            $data = json_decode(json_encode(simplexml_load_string($input, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

            libxml_disable_entity_loader($disableEntities);

            if (empty($data)) {
                exit('fail');
            }
            if ($data['result_code'] != 'SUCCESS' || $data['return_code'] != 'SUCCESS') {
                exit('fail');
            }
            $post = $data;
        } else {
            $post = $_POST;
        }

        return $post;
    }
}