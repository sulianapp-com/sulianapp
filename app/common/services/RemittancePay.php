<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/24
 * Time: 下午12:42
 */

namespace app\common\services;

use app\backend\modules\member\models\MemberRelation;
use app\common\models\PayOrder;
use app\common\models\Setting;
use app\common\services\finance\BalanceChange;
use app\frontend\modules\finance\services\BalanceService;

class RemittancePay extends Pay
{

    public function doPay($params = [])
    {
        $operation = '银行转账支付 订单号：' . $params['order_no'];
        $this->log($params['extra']['type'], '银行转账支付', $params['amount'], $operation, $params['order_no'], Pay::ORDER_STATUS_NON, \YunShop::app()->getMemberId());

        self::payRequestDataLog($params['order_no'], $params['extra']['type'], '银行转账', json_encode($params));

        $pay_order_model = PayOrder::uniacid()->where('out_order_no', $params['order_no'])->first();

        if ($pay_order_model) {
            $pay_order_model->status = 2;
            $pay_order_model->trade_no = $params['trade_no'];
            $pay_order_model->third_type = '银行转账';
            $pay_order_model->save();
        }

        // todo 从设置中获取
        $payeeInfo = [
            [   'title'=>'开户行',
                'text'=>\Setting::get('shop.pay.remittance_bank')
            ],
            [   'title'=>'开户支行',
                'text'=>\Setting::get('shop.pay.remittance_sub_bank')
            ],
            [   'title'=>'开户名',
                'text'=>\Setting::get('shop.pay.remittance_bank_account_name')
            ],
            [   'title'=>'开户账号',
                'text'=>\Setting::get('shop.pay.remittance_bank_account')
            ],
            [   'title'=>'汇款识别码',
                'text'=>$params['order_no']
            ],
            [   'title'=>'支付单号',
                'text'=>$params['order_no']
            ]

        ];

        $data = [
            'pay_sn'=>$params['order_no'],
            'amount'=>number_format($params['amount'], 2),
            'payee_info'=>$payeeInfo
        ];

        return $data;

    }

    public function doRefund($out_trade_no, $totalmoney, $refundmoney)
    {
        // TODO: Implement doRefund() method.
    }

    public function doWithdraw($member_id, $out_trade_no, $money, $desc, $type)
    {
        // TODO: Implement doWithdraw() method.
    }

    public function buildRequestSign()
    {
        // TODO: Implement buildRequestSign() method.
    }
}