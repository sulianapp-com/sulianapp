<?php


namespace app\frontend\modules\order\fee;


use app\frontend\models\order\PreOrderFee;
use app\frontend\modules\order\models\PreOrder;

abstract class BaseOrderFee
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
        // 将手续费金额保存在订单手续费表中
        $preOrderFee = new PreOrderFee([
            'fee_code' => $this->code,
            'amount' => $this->amount,
            'name' => $this->getName(),

        ]);
        $preOrderFee->setOrder($this->order);
        return $this->amount;
    }
    public function getCode(){
        return $this->code;
    }
    public function getName(){
        return $this->name;
    }
    abstract protected function _getAmount();
    public function enable(){
        return true;
    }
}