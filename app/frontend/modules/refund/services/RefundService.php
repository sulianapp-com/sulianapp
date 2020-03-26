<?php
namespace app\frontend\modules\refund\services;

use app\common\exceptions\AppException;
use app\common\models\Order;
use app\common\models\refund\RefundApply;
use app\frontend\modules\refund\services\operation\RefundSend;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/13
 * Time: 下午2:21
 */
class RefundService
{
    public static function createOrderRN()
    {
        $refundSN = createNo('RN', true);
        while (1) {
            if (!RefundApply::where('refund_sn', $refundSN)->first()) {
                break;
            }
            $refundSN = createNo('RN', true);
        }
        return $refundSN;
    }

}