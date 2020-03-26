<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/11
 * Time: 下午3:57
 */

namespace app\common\listeners\point;

use app\common\events\order\AfterOrderCanceledEvent;
use app\common\events\order\AfterOrderCreatedImmediatelyEvent;
use app\common\events\order\AfterOrderReceivedEvent;
use app\common\models\finance\PointQueue;
use app\common\models\Order;
use app\common\models\UniAccount;
use app\common\services\finance\CalculationPointService;
use app\common\services\finance\PointQueueService;
use app\common\services\finance\PointRollbackService;
use app\common\services\finance\PointService;
use app\frontend\modules\finance\services\AfterOrderDeductiblePointService;
use app\Jobs\OrderBonusJob;
use app\Jobs\PointToLoveJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Setting;

class PointListener
{
    use DispatchesJobs;

    /**
     * @var
     */
    private $pointSet;

    /**
     * @var
     */
    private $orderModel;


    public function subscribe($events)
    {
        /**
         * 收货之后 根据商品和订单赠送积分
         */
        $events->listen(
            AfterOrderReceivedEvent::class,
            PointListener::class . '@changePoint'
        );

        /**
         * 订单关闭 积分抵扣回滚
         */
        $events->listen(
            AfterOrderCanceledEvent::class,
            PointRollbackService::class . '@orderCancel'
        );

        /**
         * 积分每月赠送
         */
        $events->listen('cron.collectJobs', function () {
            \Cron::add('PointQueue', '*/30 * * * * *', function() {
                $pointQueueService = new PointQueueService();
                $pointQueueService->handle();
            });
        });

        /**
         * 积分自动转入爱心值
         */
        $events->listen('cron.collectJobs', function() {

            \Log::info("--积分自动转入爱心值检测--");
            $uniAccount = UniAccount::getEnable();
            foreach ($uniAccount as $u) {
                \YunShop::app()->uniacid = $u->uniacid;
                \Setting::$uniqueAccountId = $uniacid = $u->uniacid;

                $point_set = Setting::get('point.set');

                if (isset($point_set['transfer_love'])
                    && $point_set['transfer_love'] == 1
                    && \YunShop::plugin()->get('love')
                    && Setting::get('point.last_to_love_time') != date('d')
                    && date('H') == 1
                ) {

                    \Log::info("--积分自动转入爱心值Uniacid:{$u->uniacid}加入队列--");
                    \Cron::add("Point_To_Love{$u->uniacid}", '*/30 * * * * *', function () use($uniacid) {
                        (new PointToLoveJob($uniacid))->handle();
                    });
                    \Setting::set('point.last_to_love_time',date('d'));
                } else {
                    \Log::info("--积分自动转入爱心值Uniacid:{$u->uniacid}未满足条件--",print_r($point_set,true));
                }
            }
        });
    }

    /**
     * 收货之后 根据商品和订单赠送积分
     *
     * @param AfterOrderReceivedEvent $event
     */
    public function changePoint(AfterOrderReceivedEvent $event)
    {
        $this->orderModel = Order::find($event->getOrderModel()->id);
        $this->pointSet = $this->orderModel->getSetting('point.set');
        // 订单商品赠送积分[ps:商品单独设置]
//        $this->givingTime($this->orderModel);
        self::byGoodsGivePoint($this->orderModel);
        // 订单金额赠送积分[ps:积分基础设置]
        $this->orderGivePoint($this->orderModel);

        // 订单插件分红记录
        (new OrderBonusJob('yz_point_log', 'point', 'order_id', 'id', 'point', $this->orderModel))->handle();
    }

//    private function givingTime($orderModel)
//    {
//        $data = self::byGoodsGivePoint($orderModel);
////      每月赠送
//        if ($data['goodsSale']['point_type'] && $data['goodsSale']['max_once_point'] > 0) {
//                PointQueue::handle($this->orderModel, $data['goodsSale'], $data['point_data']['point']);
//        } else {
//        // 订单完成立即赠送[ps:原业务逻辑]
//            $this->addPointLog($data['point_data']);
//        }
//    }

    public function getPointDataByGoods($order_goods_model)
    {
        $pointData = [
            'point_income_type' => 1,
            'member_id' => $this->orderModel->uid,
            'order_id' => $this->orderModel->id,
            'point_mode' => 1
        ];
        $pointData += CalculationPointService::calcuationPointByGoods($order_goods_model);
        return $pointData;
    }

    public function getPointDateByOrder($orderModel)
    {
        $pointData = [
            'point_income_type' => 1,
            'member_id' => $this->orderModel->uid,
            'order_id' => $this->orderModel->id,
            'point_mode' => 2
        ];

        $pointData += CalculationPointService::calcuationPointByOrder($orderModel);
        return $pointData;
    }

    private function addPointLog($pointData)
    {
        if (isset($pointData['point'])) {
            $pointService = new PointService($pointData);
            $pointService->changePoint();
        }
    }

    public function byGoodsGivePoint($orderModel)
    {

        // 验证订单商品是立即赠送还是每月赠送
        foreach ($orderModel->hasManyOrderGoods as $orderGoods) {
            // 商品营销数据
            $goodsSale = $orderGoods->hasOneGoods->hasOneSale;
            // 赠送积分数组[ps:放到这是因为(每月赠送)需要赠送积分总数]
            $point_data = self::getPointDataByGoods($orderGoods);
            // 每月赠送
            if ($goodsSale->point_type && $goodsSale->max_once_point > 0) {
                PointQueue::handle($this->orderModel, $goodsSale, $point_data['point']);
            } else {
                // 订单完成立即赠送[ps:原业务逻辑]
                self::addPointLog($point_data);
            }
        }
    }


    public function byGoodsGivePointPay($orderModel)
    {
        $point = 0;
        // 验证订单商品是立即赠送还是每月赠送
        foreach ($orderModel->hasManyOrderGoods as $orderGoods) {
            // 赠送积分数组[ps:放到这是因为(每月赠送)需要赠送积分总数]
            $point_data = self::getPointDataByGoods($orderGoods);
            $point += $point_data['point'];
            // 每月赠送
        }
        return $point;
    }


//    private function byGoodsGivePoint()
//    {
//        // 验证订单商品是立即赠送还是每月赠送
//        foreach ($this->orderModel->hasManyOrderGoods as $orderGoods) {
//            // 商品营销数据
//            $goodsSale = $orderGoods->hasOneGoods->hasOneSale;
//            // 赠送积分数组[ps:放到这是因为(每月赠送)需要赠送积分总数]
//            $point_data = $this->getPointDataByGoods($orderGoods);
//            // 每月赠送
//            if ($goodsSale->point_type && $goodsSale->max_once_point > 0) {
//                PointQueue::handle($this->orderModel, $goodsSale, $point_data['point']);
//            } else {
//                // 订单完成立即赠送[ps:原业务逻辑]
//                $this->addPointLog($point_data);
//            }
//        }
//    }

    private function orderGivePoint($orderModel)
    {
        \Log::debug('赠送积分');
        $pointData = $this->getPointDateByOrder($orderModel);
        $this->addPointLog($pointData);
    }


}
