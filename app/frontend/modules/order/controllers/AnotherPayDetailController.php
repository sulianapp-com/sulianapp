<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/1/18
 * Time: 下午2:19
 */

namespace app\frontend\modules\order\controllers;


use app\common\components\ApiController;
use app\common\models\DispatchType;
use app\frontend\models\AnotherPayOrder;
use app\frontend\models\OrderAddress;
use app\frontend\modules\order\services\VideoDemandOrderGoodsService;

class AnotherPayDetailController extends ApiController
{
    public function index($request)
    {
        $this->validate([
            'order_id' => 'required'
        ]);

        $order_ids = explode(',', $request->query('order_id'));

        foreach ($order_ids as $orderId) {
            $order = $this->getOrder()->with(['hasManyOrderGoods','orderDeduction','orderDiscount','orderCoupon','orderFees'])->find($orderId);

            if (is_null($order)) {
                return $this->errorJson($msg = '未找到数据', []);
            }

            $data = $order->toArray();
            $backups_button = $data['button_models'];
            $data['button_models'] = array_merge($data['button_models'],$order->getStatusService()->getRefundButtons($order));
            //$this->getStatusService()->
            //todo 配送类型
            if ($order['dispatch_type_id'] == DispatchType::EXPRESS) {
                $data['address_info'] = OrderAddress::select('address', 'mobile', 'realname')->where('order_id', $order['id'])->first();
            }
            if(app('plugins')->isEnabled('store-cashier')){

                //临时解决
                $storeObj = \Yunshop\StoreCashier\common\models\Store::getStoreByCashierId($order->hasManyOrderGoods[0]->goods_id)->first();
                if ($storeObj) {
                    $data['button_models'] = $backups_button;
                }

                if ($order['dispatch_type_id'] == DispatchType::SELF_DELIVERY) {
                    $data['address_info'] = \Yunshop\StoreCashier\common\models\SelfDelivery::where('order_id', $order['id'])->first();
                }elseif($order['dispatch_type_id'] == DispatchType::STORE_DELIVERY){
                    $data['address_info'] = \Yunshop\StoreCashier\common\models\StoreDelivery::where('order_id', $order['id'])->first();
                }
            }

            //todo 临时解决
            if (!$order) {
                return $this->errorJson($msg = '未找到数据', []);
            } else {
                //视频点播
                if (VideoDemandOrderGoodsService::whetherEnabled()) {
                    foreach ($data['has_many_order_goods'] as &$value) {
                        $value['is_course'] = VideoDemandOrderGoodsService::whetherCourse($value['goods_id']);
                    }
                }
            }

            $result[] = $data;
        }

        return $this->successJson($msg = 'ok', $result);
    }

    protected function getOrder()
    {
        return new AnotherPayOrder();
    }
}