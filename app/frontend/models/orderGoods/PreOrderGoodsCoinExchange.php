<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/7/25
 * Time: 下午7:10
 */

namespace app\frontend\models\orderGoods;

use app\common\models\orderGoods\OrderGoodsCoinExchange;
use app\common\models\VirtualCoin;
use app\common\modules\orderGoods\models\PreOrderGoods;
use app\frontend\models\MemberCoin;

class PreOrderGoodsCoinExchange extends OrderGoodsCoinExchange
{
    /**
     * @var PreOrderGoods
     */
    public $orderGoods;

    public function setOrderGoods(PreOrderGoods $orderGoods)
    {
        $this->orderGoods = $orderGoods;
        $this->uid = $this->orderGoods->uid;
        $orderGoods->setRelation('orderGoodsCoinExchange',$this);
        $this->getMemberCoin()->lockCoin($this->coin);
    }

    public function save(array $options = [])
    {
        $this->getMemberCoin()->consume($this->newCoin()->setCoin($this->coin), ['order_sn' => $this->orderGoods->order->order_sn]);
        return parent::save($options);
    }

    /**
     * 此抵扣对应的虚拟币
     * @return VirtualCoin
     */
    private function newCoin()
    {
        return app('CoinManager')->make($this->code);
    }

    /**
     * 下单用户此抵扣对应虚拟币的余额
     * @return MemberCoin
     */
    private function getMemberCoin()
    {
        if (isset($this->memberCoin)) {
            return $this->memberCoin;
        }

        return app('CoinManager')->make('MemberCoinManager')->make($this->code, [$this->orderGoods->order->belongsToMember]);
    }

}