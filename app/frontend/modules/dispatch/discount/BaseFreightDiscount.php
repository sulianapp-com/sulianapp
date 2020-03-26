<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/5/23
 * Time: 下午2:21
 */

namespace app\frontend\modules\dispatch\discount;

use app\frontend\models\order\PreOrderDiscount;
use app\frontend\modules\order\models\PreOrder;

abstract class BaseFreightDiscount
{
    /**
     * @var PreOrder
     */
    protected $order;
    protected $price;
    protected $name;
    protected $code;

    public function __construct(PreOrder $order)
    {
        $this->order = $order;
    }

    /**
     * 全场满额包邮
     * @return bool
     */
    public function getAmount()
    {
        if (!isset($this->price)) {
            $amount = $this->_getAmount();
            if ($amount > 0) {
                $preOrderDiscount = new PreOrderDiscount([
                    'discount_code' => $this->code,
                    'amount' => $amount,
                    'name' => $this->name,

                ]);
                $preOrderDiscount->setOrder($this->order);
            }
            $this->price = $amount;
        }
        return $this->price;
    }

    abstract protected function _getAmount();
}