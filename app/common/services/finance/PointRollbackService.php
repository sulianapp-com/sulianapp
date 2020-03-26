<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/9/1 下午4:33
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\common\services\finance;


use app\common\facades\Setting;
use app\common\models\Order;

class PointRollbackService
{
    /**
     * @var Order
     */
    private $orderModel;

    /**
     * @param $event
     * @throws \app\common\exceptions\ShopException
     */
    public function orderCancel($event)
    {
        if (!Setting::get('point.set.point_rollback')) {
            return;
        }

        $this->orderModel = $event->getOrderModel();
        $this->rollbackCoinExchange();
        $pointDeduction = $this->getOrderPointDeduction();
        if (!$pointDeduction) {
            return;
        }
        $this->pointRollback($pointDeduction);

        /**
         * 返还全额抵扣的积分
         */

        return;
    }

    /**
     * 返还全额抵扣积分
     * @throws \app\common\exceptions\ShopException
     */
    private function rollbackCoinExchange()
    {
        $point = $this->orderModel->orderCoinExchanges->where('code', 'point')->first();
        if (!$point) {
            return;
        }
        $coin = $point['coin'];
        $data = [
            'point_income_type' => PointService::POINT_INCOME_GET,
            'point_mode' => PointService::POINT_MODE_ROLLBACK,
            'member_id' => $this->orderModel->uid,
            'point' => $coin,
            'remark' => '订单：' . $this->orderModel->order_sn . '关闭，返还积分抵扣积分' . $coin,
        ];
        (new PointService($data))->changePoint();
    }

    private function getOrderPointDeduction()
    {
        $point = 0;
        if ($this->orderModel->deductions) {
            foreach ($this->orderModel->deductions as $key => $deduction) {
                if ($deduction['code'] == 'point') {
                    $point = $deduction['coin'];
                    break;
                }
            }
        }

        return $point;
    }

    private function pointRollback($point)
    {
        return (new PointService($this->getChangeData($point)))->changePoint();
    }

    private function getChangeData($point)
    {
        return [
            'point_income_type' => PointService::POINT_INCOME_GET,
            'point_mode' => PointService::POINT_MODE_ROLLBACK,
            'member_id' => $this->orderModel->uid,
            'point' => $point,
            'remark' => '订单：' . $this->orderModel->order_sn . '关闭，返还积分抵扣积分' . $point,
        ];
    }

}
