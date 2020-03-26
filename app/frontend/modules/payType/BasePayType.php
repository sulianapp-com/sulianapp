<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/7
 * Time: 下午4:45
 */

namespace app\frontend\modules\payType;

use app\common\exceptions\AppException;
use app\common\models\OrderPay;
use app\common\models\PayType;

class BasePayType extends PayType implements OrderPayInterface
{
    /**
     * @var OrderPay
     */
    protected $orderPay;

    public function setOrderPay(OrderPay $orderPay)
    {
        $this->orderPay = $orderPay;

    }

    public function applyPay()
    {
    }

    /**
     * @param array $option
     * @return array
     * @throws AppException
     */
    function getPayParams($option)
    {
        $extra = ['type' => 1];

        if (!is_array($option)) {
            throw new AppException('参数类型错误');
        }

        $extra = array_merge($extra, $option);

        return [
            'order_no' => $this->orderPay->pay_sn,
            'amount' => $this->orderPay->orders->sum('price'),
            'subject' => $this->orderPay->orders->first()->hasManyOrderGoods[0]->title ?: ' ',
            'body' => ($this->orderPay->orders->first()->hasManyOrderGoods[0]->title ?: ' ') . ':' . \YunShop::app()->uniacid,
            'extra' => $extra
        ];
    }
}