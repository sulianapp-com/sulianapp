<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/13
 * Time: 下午4:14
 */

namespace app\frontend\modules\order\models;


use app\common\exceptions\AppException;

trait PreOrderTrait
{
    /**
     * 订单插入数据库,触发订单生成事件
     * @return mixed
     * @throws AppException
     */
    public function generate()
    {
        $this->beforeSaving();
        $this->save();
        $this->afterSaving();
        $result = $this->push();

        if ($result === false) {

            throw new AppException('订单相关信息保存失败');
        }
        return $this->id;
    }


    /**
     * 统计商品总数
     * @return int
     */
    protected function getGoodsTotal()
    {
        //累加所有商品数量
        $result = $this->orderGoods->sum(function ($aOrderGoods) {
            return $aOrderGoods->total;
        });

        return $result;
    }

    /**
     * 统计订单商品成交金额
     * @return int
     */
    public function getOrderGoodsPrice()
    {
        return $this->goods_price = $this->orderGoods->getPrice();
    }

    /**
     * 统计订单商品原价
     * @return int
     */
    public function getGoodsPrice()
    {
        return $this->orderGoods->getGoodsPrice();
    }

    public function getPriceAttribute()
    {
        return $this->getPrice();
    }

    public function getDispatchPriceAttribute()
    {
        return $this->getDispatchAmount();
    }
}