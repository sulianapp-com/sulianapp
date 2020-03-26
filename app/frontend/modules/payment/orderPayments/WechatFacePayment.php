<?php


namespace app\frontend\modules\payment\orderPayments;


class WechatFacePayment extends BasePayment
{
    public function canUse()
    {
        //商家pos才支持扫码支付
        if (\Yunshop::request()->type != 9) {
            return false;
        }
        return parent::canUse() && \Yunshop::request()->type == 9;
    }
}