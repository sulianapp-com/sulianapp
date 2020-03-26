<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/3
 * Time: 上午9:10
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\models\DispatchType;
use app\common\models\Order;
use app\frontend\models\OrderAddress;
use app\common\services\plugin\leasetoy\LeaseToySet;
use app\common\services\goods\VideoDemandCourseGoods;

class DetailController extends ApiController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     * @throws \app\common\exceptions\ShopException
     */
    public function index($request)
    {
        $this->validate([
            'order_id' => 'required|integer'
        ]);
        $orderId = $request->query('order_id');
        $order = $this->getOrder()->with(['hasManyOrderGoods','orderDeduction','orderDiscount','orderFees','orderCoupon'])->find($orderId);
//        if ($order->uid != \YunShop::app()->getMemberId()) {
//            throw new AppException('(ID:' . $order->id . ')该订单属于其他用户');
//        }
        $invoice=Order::getInvoice($orderId);

        if (is_null($order)) {
            return $this->errorJson('未找到数据', []);
        }
        $data = $order->toArray();
        $invoice->invoice = ("0" != $invoice->invoice) ? 1 : 0;
        $data['invoice_type'] =$invoice->invoice_type;
        $data['rise_type'] =$invoice->rise_type;
        $data['collect_name'] =$invoice->collect_name;
        $data['company_number'] =$invoice->company_number;
        $data['invoice_state'] = $invoice->invoice;
        $backups_button = $data['button_models'];

        $data['address_info'] = OrderAddress::select('address', 'mobile', 'realname')->where('order_id', $order['id'])->first()?:[];

        if(app('plugins')->isEnabled('store-cashier')){

            //加入门店ID，订单跳转商品详情需要
            $store_id = \Yunshop\StoreCashier\store\models\StoreGoods::select()->byGoodsId($order->hasManyOrderGoods[0]->goods_id)->first()->store_id;
            $data['has_many_order_goods']['0']['store_id'] = $store_id;

            //临时解决
            $storeObj = \Yunshop\StoreCashier\common\models\Store::getStoreByCashierId($order->hasManyOrderGoods[0]->goods_id)->first();

            if ($storeObj) {
                $data['button_models'] = $backups_button;
            }

            if ($order['dispatch_type_id'] == DispatchType::SELF_DELIVERY) {
                // $data['address_info'] = \Yunshop\StoreCashier\common\models\SelfDelivery::where('order_id', $order['id'])->first();

            }elseif($order['dispatch_type_id'] == DispatchType::STORE_DELIVERY){
                // $data['address_info'] = \Yunshop\StoreCashier\common\models\StoreDelivery::where('order_id', $order['id'])->first();
            }

        }


        $videoDemand = new VideoDemandCourseGoods();
        foreach ($data['has_many_order_goods'] as &$value) {
            $value['thumb'] = yz_tomedia($value['thumb']);
            //视频点播
            $value['is_course'] = $videoDemand->isCourse($value['goods_id']);
        }

        $configs = \app\common\modules\shop\ShopConfig::current()->get('shop-foundation.order.order_detail');
        if($configs) {
            foreach ($configs as $pluginName => $pluginOperators) {
                $class = array_get($pluginOperators,'class');
                $function = array_get($pluginOperators,'api_function');
                if(class_exists($class) && method_exists($class,$function) && is_callable([$class,$function])) {
                    $plugin_data = $class::$function($data['id']);
                    if ($plugin_data) {
                        $data[$pluginName] = $plugin_data;
                    }
                }
            }
        }

        return $this->successJson($msg = 'ok', $data);
    }

    protected function getOrder()
    {
        return app('OrderManager')->make('Order');
    }
}