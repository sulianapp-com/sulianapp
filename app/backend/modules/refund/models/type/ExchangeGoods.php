<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/21
 * Time: 下午4:34
 */

namespace app\backend\modules\refund\models\type;

use app\common\models\refund\RefundApply;

class ExchangeGoods extends ReturnGoods
{
    public function resend()
    {

        $this->refundApply->status = RefundApply::WAIT_RECEIVE_RESEND_GOODS;
        return $this->refundApply->save();
    }
    public function close()
    {
        $this->refundApply->status = RefundApply::CLOSE;
        return $this->refundApply->save();

    }
}