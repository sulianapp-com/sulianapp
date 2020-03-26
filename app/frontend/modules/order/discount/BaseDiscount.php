<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/5/23
 * Time: 下午3:55
 */

namespace app\frontend\modules\order\discount;

use app\frontend\models\order\PreOrderDiscount;
use app\frontend\modules\order\models\PreOrder;

abstract class BaseDiscount
{
    /**
     * @var PreOrder
     */
    protected $order;
    /**
     * 优惠名
     * @var string
     */
    protected $name;
    /**
     * 优惠码
     * @var
     */
    protected $code;
    /**
     * @var float
     */
    private $amount;

    public function __construct(PreOrder $order)
    {
        $this->order = $order;
    }
    public function getCode(){
        return $this->code;
    }
    /**
     * @return bool
     */
    public function calculated()
    {
        return isset($this->amount);
    }

    /**
     * 获取总金额
     * @return float
     */
    public function getAmount()
    {
        if (isset($this->amount)) {
            return $this->amount;
        }

        $this->amount = $this->_getAmount();
        if($this->amount){
            // 将抵扣总金额保存在订单优惠信息表中
            $preOrderDiscount = new PreOrderDiscount([
                'discount_code' => $this->code,
                'amount' => $this->amount,
                'name' => $this->name,

            ]);
            $preOrderDiscount->setOrder($this->order);
        }
        return $this->amount;
    }

    /**
     * 获取金额
     * @return int
     */
    abstract protected function _getAmount();

}