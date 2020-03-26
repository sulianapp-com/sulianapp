<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/13
 * Time: 下午2:17
 */

namespace app\frontend\modules\refund\controllers;


use app\common\components\ApiController;
use app\common\exceptions\AdminException;
use app\common\modules\refund\services\RefundService;
use app\frontend\modules\refund\models\RefundApply;
use app\frontend\modules\refund\services\RefundMessageService;
use app\frontend\modules\refund\services\RefundOperationService;

class OperationController extends ApiController
{
    public $transactionActions = ['*'];

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \app\common\exceptions\AppException
     * @throws \app\common\exceptions\ShopException
     */
    public function send()
    {
        $this->validate([
            'refund_id' => 'required|filled|integer',
            'express_company_code' => 'required|string',
            'express_company_name' => 'required|string',
            'express_sn' => 'required|filled|string',
        ]);
        RefundOperationService::refundSend();
        return $this->successJson();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \app\common\exceptions\AppException
     * @throws \app\common\exceptions\ShopException
     */
    public function complete()
    {
        $this->validate([
            'refund_id' => 'required|filled|integer',
        ]);

        RefundOperationService::refundComplete();

        return $this->successJson();

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \app\common\exceptions\AppException
     * @throws \app\common\exceptions\ShopException
     */
    public function cancel()
    {
        $this->validate([
            'refund_id' => 'required|filled|integer',
        ]);
        RefundOperationService::refundCancel();
        return $this->successJson();

    }

}