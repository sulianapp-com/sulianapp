<?php
/**
 * Created by PhpStorm.
 * User: 宝佳
 * Date: 2018/1/17
 * Time: 16:24
 */

namespace app\common\services\finance;


use app\common\events\order\AfterOrderReceivedEvent;
use app\common\exceptions\ShopException;
use app\common\services\credit\ConstService;

class BalanceAwardService
{
    private $orderModel;

    public function awardBalance(AfterOrderReceivedEvent $event)
    {
        $this->orderModel = $event->getOrderModel();

        $change_value = $this->getChangeValue();
        if ($change_value > 0) {
            $data = $this->getChangeData();
            $data['change_value'] = $change_value;

            $result = (new BalanceChange())->award($data);
            if ($result !== true) {
                throw new ShopException('购物赠送余额失败，请重试！');
            }
        }
    }

    private function getChangeData()
    {
        return [
            'member_id'     => $this->getUid(),
            'remark'        => "购物赠送余额",
            'relation'      => $this->orderModel->order_sn,
            'operator'      => ConstService::OPERATOR_MEMBER,
            'operator_id'   => $this->orderModel->uid,
            //'change_value'  => $this->getChangeValue(),
        ];
    }


    private function getChangeValue()
    {
       $orderGoods = $this->orderModel->hasManyOrderGoods;

        $change_value = 0;
        foreach ($orderGoods as $goodsModel)
        {
            $goodsSaleModel = $goodsModel->hasOneGoods->hasOneSale;

            if (!$goodsSaleModel || empty($goodsSaleModel->award_balance)) {
                continue;
            }
            $change_value += $this->proportionMath($goodsModel->payment_amount, $goodsSaleModel->award_balance, $goodsModel->total);
        }

        return $change_value;
    }

    private function getUid()
    {
        $orderGoods = $this->orderModel->hasManyOrderGoods;

        foreach ($orderGoods as $goodsModel)
        {
            $uid = $goodsModel->uid ?: $this->orderModel->uid;
        }

        return $uid;
    }


    private function proportionMath($price, $proportion, $total)
    {
        if (strexists($proportion, '%')) {
            $proportion = str_replace('%', '', $proportion);
            return bcdiv(bcmul($price,$proportion,4),100,2);
        }
        return bcmul($proportion,$total,2);
    }


}
