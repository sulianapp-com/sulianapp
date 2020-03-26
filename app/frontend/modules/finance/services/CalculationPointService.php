<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/11
 * Time: 下午9:44
 */

namespace app\frontend\modules\finance\services;

use app\backend\modules\member\models\Member;
use app\common\models\Order;
use Illuminate\Support\Facades\Log;

class CalculationPointService
{
    private $orderGoodsModels;
    /**
     * @var Order
     */
    private $order;
    private $point_set;
    public $point;
    private $member;
    public $point_money;

    public function __construct($order, $member_id)
    {

        $this->order = $order;
        $this->orderGoodsModels = $order->orderGoods;

        //验证积分设置
        $this->verifyPointSet();
        //验证用户积分
        $this->verifyMemberPoint($member_id);
        //计算积分
        $this->calculationPoint();

        $this->point_money = floor($this->point * $this->point_set['money'] * 100) / 100;
        Log::info("订单{$this->order->id}:用户积分使用{$this->point_money}");

    }

    /**
     * @name 验证积分设置的是否开启积分抵扣
     * @author yangyang
     * @return false
     */
    private function verifyPointSet()
    {
        Log::info("订单{$this->order->id}:订单积分设置");
        if ($this->order->getSetting('point.set.point_deduct') == 0) {
            return false;
        }
        $this->point_set = $this->order->getSetting('point.set');
    }

    /**
     * @name 验证用户是否有积分
     * @author yangyang
     * @param $member_id
     * @return false
     */
    private function verifyMemberPoint($member_id)
    {
        Log::info("订单{$this->order->id}:用户积分验证");
        if (Member::getMemberInfoById($member_id)['credit1'] <= 0) {
            return false;
        }
        $this->member = Member::getMemberInfoById($member_id);
    }

    /**
     * @name 计算可以使用多少积分
     * @author yangyang
     */
    private function calculationPoint()
    {
        foreach ($this->orderGoodsModels as $goodsModel) {
            $this->calculationMemberPoint($this->getGoodsPoint($goodsModel));
        }
    }

    /**
     * @name 获取商品
     * @author yangyang
     * @param $goods_model
     * @return float|int
     */
    private function getGoodsPoint($goods_model)
    {
        if ($goods_model->goods->hasOneSale->max_point_deduct === '0') {
            return 0;
        }
        if ($goods_model->goods->hasOneSale->max_point_deduct) {
            if (strexists($goods_model->goods->hasOneSale->max_point_deduct, '%')) {
                // 独立价格比例
                $goods_point = floatval(str_replace('%', '', $goods_model->goods->hasOneSale->max_point_deduct) / 100 * $goods_model->goods_price / $this->point_set['money']);
            } else {
                // 独立固定金额
                $goods_point = $goods_model->goods->hasOneSale->max_point_deduct * $goods_model->total / $this->point_set['money'];
            }
            return $goods_point;
        } else if ($this->point_set['money_max'] > 0 && empty($goods_model->goods->hasOneSale->max_point_deduct)) {
            // 全局比例
            $goods_point = $this->point_set['money_max'] / 100 * $goods_model->price / $this->point_set['money'];
            return $goods_point;
        }
    }

    private function calculationMemberPoint($goods_point)
    {
        if ($goods_point >= $this->member['credit1']) {
            $this->point += $this->member['credit1'];
            $this->member['credit1'] = 0;
        } else {
            $this->point += $goods_point;
            $this->member['credit1'] = $this->member['credit1'] - $goods_point;
        }
    }
}