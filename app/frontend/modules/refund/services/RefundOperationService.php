<?php

namespace app\frontend\modules\refund\services;

use app\common\exceptions\AppException;
use app\frontend\modules\refund\services\operation\ExchangeComplete;
use app\frontend\modules\refund\services\operation\ReceiveResendGoods;
use app\frontend\modules\refund\services\operation\RefundCancel;
use app\frontend\modules\refund\services\operation\RefundSend;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/13
 * Time: 下午2:21
 */
class RefundOperationService
{
    /**
     * @return mixed
     * @throws AppException
     */
    public static function refundSend()
    {
        //todo 需要与后台操作统一
        $refundSend = RefundSend::find(request()->input('refund_id'));
        if (!$refundSend) {
            throw new AppException('售后申请记录不存在');
        }
        $refundSend->enable();
        return $refundSend->execute();

    }

    /**
     * @return mixed
     * @throws AppException
     */
    public static function exchangeComplete(){
    //todo 需要与后台操作统一
        $exchangeComplete = ExchangeComplete::find(request()->input('refund_id'));
        if (!$exchangeComplete) {
            throw new AppException('售后申请记录不存在');
        }
        $exchangeComplete->enable();
        return $exchangeComplete->execute();
    }

    /**
     * @return mixed
     * @throws AppException
     */
    public static function refundCancel()
    {
        //todo 需要与后台操作统一
        $refundCancel = RefundCancel::find(request()->input('refund_id'));
        if (!$refundCancel) {
            throw new AppException('售后申请记录不存在');
        }
        $refundCancel->enable();
        return $refundCancel->execute();

    }

    /**
     * @return mixed
     * @throws AppException
     */
    public static function refundComplete()
    {
        //todo 需要与后台操作统一
        $refundComplete = ReceiveResendGoods::find(request()->input('refund_id'));
        if (!$refundComplete) {
            throw new AppException('售后申请记录不存在');
        }
        $refundComplete->enable();
        return $refundComplete->execute();

    }
}