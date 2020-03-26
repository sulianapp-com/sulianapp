<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/9/10
 * Time: 上午11:30
 */

namespace app\common\models;

/**
 * Class VirtualCoin
 * @package app\common\models
 * @property float amountOfCoin
 * @property float amountOfMoney
 */
abstract class VirtualCoin extends BaseModel
{
    protected $table = 'yz_virtual_coin';
    protected $attributes = [
        'amountOfCoin' => 0,
        'amountOfMoney' => 0,
    ];
    protected $_name;
    protected $code;
    protected $appends = ['name'];
    protected $exchange_rate;

    function __construct($attribute = [])
    {
        parent::__construct($attribute);
        $this->exchange_rate = $this->getExchangeRate();
        $this->_name = $this->getName();
        $this->code = $this->getCode();
    }

    public function getNameAttribute()
    {
        return $this->getName();
    }

    public function getCode()
    {
        return isset($this->code) ? $this->code : $this->code = $this->_getCode();
    }

    public function getName()
    {
        return isset($this->_name) ? $this->_name : $this->_name = $this->_getName();
    }

    public function getExchangeRate()
    {
        return isset($this->exchange_rate) ? $this->exchange_rate : ($this->exchange_rate = $this->_getExchangeRate() ?: 1);
    }

    abstract protected function _getExchangeRate();

    abstract protected function _getName();

    abstract protected function _getCode();

    /**
     * @param VirtualCoin $coin
     * @return VirtualCoin
     */
    public function plus(VirtualCoin $coin)
    {
        return (new static())->setMoney($this->amountOfMoney + $coin->getMoney());
    }

    public function setCoin($amount)
    {
        $this->amountOfMoney = $amount * $this->exchange_rate;
        return $this;
    }

    public function setMoney($amount)
    {

        $this->amountOfMoney = $amount;
        return $this;
    }

    public function toArray()
    {
        $this->amountOfCoin = sprintf('%.2f', $this->getCoin());

        $this->amountOfMoney = sprintf('%.2f', $this->getMoney());

        return parent::toArray();
    }

    /**
     * @return float|int
     */
    public function getCoin()
    {
        return $this->amountOfCoin = bcdiv($this->amountOfMoney,$this->exchange_rate,2);;
    }

    /**
     * @return mixed
     */
    public function getMoney()
    {
        return $this->amountOfMoney;
    }

    public function save(array $options = [])
    {
        return true;
    }
}