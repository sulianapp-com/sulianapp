<?php

namespace app\payment;

use app\common\components\BaseController;
use app\common\events\payment\ChargeComplatedEvent;
use app\common\events\payment\RechargeComplatedEvent;
use app\common\models\AccountWechats;
use app\common\models\OrderPay;
use app\common\models\PayOrder;
use app\frontend\modules\finance\services\BalanceRechargeResultService;
use app\frontend\modules\order\services\OrderService;
use Yunshop\ClockIn\models\ClockPayLogModel;
use Yunshop\Gold\frontend\services\RechargeService;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 24/03/2017
 * Time: 09:06
 */
class PaymentController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->init();
    }

    private function init()
    {
        $script_info = pathinfo($_SERVER['SCRIPT_NAME']);
        \Log::debug('init');
        \Log::debug($script_info);
        if (!empty($script_info)) {
            switch ($script_info['filename']) {
                case 'notifyUrl':
                    \YunShop::app()->uniacid = $this->getUniacid();
                    break;
                case 'refundNotifyUrl':
                case 'withdrawNotifyUrl':
                    $batch_no = !empty($_REQUEST['batch_no']) ? $_REQUEST['batch_no'] : '';

                    \YunShop::app()->uniacid = (int)substr($batch_no, 17, 5);
                    break;
                case 'returnUrl':
                    if (strpos($_GET['out_trade_no'], '_') !== false) {
                        $data = explode('_', $_GET['out_trade_no']);
                        \YunShop::app()->uniacid = $data[0];
                    } else {
                        \YunShop::app()->uniacid = $this->getUniacid();
                    }
                    break;
                default:
                    \YunShop::app()->uniacid = $this->getUniacid();
                    break;
            }
        }

        \Setting::$uniqueAccountId = \YunShop::app()->uniacid;

        AccountWechats::setConfig(AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid));
    }

    /**
     * 支付宝获取当前公众号
     *
     * @return int
     */
    private function getUniacid()
    {
        $body = !empty($_REQUEST['body']) ? $_REQUEST['body'] : '';
        \Log::debug('body===========',$body);
        //区分app支付获取
        if ($_REQUEST['sign_type'] == 'MD5') {
            $uniacid = substr($body, strrpos($body, ':')+1);
        } else {
            $uniacid = $this->substr_var($_REQUEST['body']);
        }
        \Log::debug('body获取unicid', $uniacid);
        if (!empty($uniacid)) {
            return intval($uniacid);
        } else {
            return 0;
        }
    }

    /**
     * 去除前后引号
     *
     * @param $value
     * @return bool|string
     */
    public function substr_var($str)
    {
        if (strstr($str, '"')) {
            return str_replace('"','', $str);
        }
        return $str;
    }

    /**
     * 支付回调操作
     *
     * @param $data
     */
    public function payResutl($data)
    {
        $type = $this->getPayType($data['out_trade_no']);
        $pay_order_model = PayOrder::getPayOrderInfo($data['out_trade_no'])->first();

        if ($pay_order_model) {
            $pay_order_model->status = 2;
            $pay_order_model->trade_no = $data['trade_no'];
            $pay_order_model->third_type = $data['pay_type'];
            $pay_order_model->save();
        }

        switch ($type) {
            case "charge.succeeded":
                \Log::debug("{$data['out_trade_no']}支付操作", ['charge.succeeded']);

                $orderPay = OrderPay::where('pay_sn', $data['out_trade_no'])->orderBy('id', 'desc')->first();

                if ($data['unit'] == 'fen') {
                    $orderPay->amount = $orderPay->amount * 100;
                }

                if (bccomp($orderPay->amount, $data['total_fee'], 2) == 0) {
                    \Log::debug('更新订单状态');
                    OrderService::ordersPay(['order_pay_id' => $orderPay->id, 'pay_type_id' => $data['pay_type_id']]);

                    event(new ChargeComplatedEvent([
                        'order_sn' => $data['out_trade_no'],
                        'pay_sn' => $data['trade_no'],
                        'order_pay_id' => $orderPay->id
                    ]));
                }else{
                    \Log::debug("金额校验失败","{$orderPay->amount}不等于{$data['total_fee']}");
                }
                break;
            case "recharge.succeeded":
                \Log::debug('支付操作', ['recharge.succeeded', $data['out_trade_no']]);

                //充值成功事件
                event(new RechargeComplatedEvent([
                    'order_sn' => $data['out_trade_no'],
                    'pay_sn' => $data['trade_no'],
                    'total_fee' => $data['total_fee'],
                    'unit' => $data['unit']
                ]));

                break;
            case "gold_recharge.succeeded":
                \Log::debug('金币支付操作', ['gold_recharge.succeeded', $data['out_trade_no']]);
                RechargeService::payResult([
                    'order_sn' => $data['out_trade_no'],
                    'pay_sn' => $data['trade_no'],
                    'total_fee' => $data['total_fee']
                ]);

                //充值成功事件
                event(new RechargeComplatedEvent([
                    'order_sn' => $data['out_trade_no'],
                    'pay_sn' => $data['trade_no'],
                    'total_fee' => $data['total_fee']
                ]));
                break;
            case "card_charge.succeeded":
                \Log::debug('打卡支付操作', ['card_charge.succeeded', $data['out_trade_no']]);

                $orderPay = ClockPayLogModel::where('order_sn', $data['out_trade_no'])->first();

                if ($data['unit'] == 'fen') {
                    $orderPay->amount = $orderPay->amount * 100;
                }

                if (bccomp($orderPay->amount, $data['total_fee'], 2) == 0) {
                    \Log::debug('更新订单状态');
                    event(new ChargeComplatedEvent([
                        'order_sn' => $data['out_trade_no'],
                        'pay_sn' => $data['trade_no'],
                        'total_fee' => $data['total_fee']
                    ]));
                }
                break;
            case "dashang_charge.succeeded":
                \Log::debug('打赏支付操作', ['dashang_charge.succeeded', $data['out_trade_no']]);
                event(new ChargeComplatedEvent([
                    'order_sn' => $data['out_trade_no'],
                    'pay_sn' => '',
                    'unit' => $data['unit'],
                    'total_fee' => $data['total_fee']
                ]));
                break;
        }
    }

    /**
     * 支付方式
     *
     * @param $order_id
     * @return string
     */
    public function getPayType($order_id)
    {
        if (!empty($order_id)) {
            $tag = substr($order_id, 0, 2);

            if ('PN' == strtoupper($tag)) {
                return 'charge.succeeded';
            } elseif ('RV' == strtoupper($tag) || "RF" == strtoupper($tag) || "RL" == strtoupper($tag)) {
                return 'recharge.succeeded';
            } elseif ('RG' == strtoupper($tag)) {
                return 'gold_recharge.succeeded';
            } elseif ('CI' == strtoupper($tag)) {
                return 'card_charge.succeeded';
            } elseif ('DS' == strtoupper($tag)) {
                return 'dashang_charge.succeeded';
            }
        }

        return '';
    }
}
