<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/3
 * Time: 下午3:43
 */

namespace app\frontend\modules\refund\services\operation;

use app\frontend\modules\order\services\OrderService;


class ReceiveResendGoods extends ChangeStatusOperation
{
    protected $statusBeforeChange = [self::WAIT_RECEIVE_RESEND_GOODS];
    protected $statusAfterChanged = self::COMPLETE;
    protected $name = '收货';
    //protected $timeField = 'send_time';

    protected $pastTenseClassName = '';

    protected function updateTable()
    {
        parent::updateTable();
    }

    /**
     * @return bool|void
     * @throws \app\common\exceptions\AppException
     */
    public function execute()
    {
        parent::execute();
        if($this->status == 2){
            OrderService::orderReceive(['order_id'=>$this->order_id]);
        }
    }
}