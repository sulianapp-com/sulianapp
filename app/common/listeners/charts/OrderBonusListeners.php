<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/6
 * Time: 11:18
 */
namespace app\common\listeners\charts;

use app\backend\modules\charts\models\OrderIncomeCount;
use app\common\events\order\AfterOrderCanceledEvent;
use app\common\events\order\AfterOrderCreatedImmediatelyEvent;
use app\common\events\order\AfterOrderPaidImmediatelyEvent;
use app\common\events\order\AfterOrderReceivedEvent;
use app\common\events\order\AfterOrderPaidEvent;
use app\common\events\order\AfterOrderCreatedEvent;
use app\common\events\order\AfterOrderReceivedImmediatelyEvent;
use app\common\events\order\AfterOrderRefundedEvent;
use app\common\models\order\OrderPluginBonus;
use app\Jobs\OrderBonusStatusJob;
use app\Jobs\OrderCountContentJob;
use app\Jobs\OrderMemberMonthJob;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Yunshop\StoreCashier\common\models\CashierOrder;
use Yunshop\StoreCashier\common\models\StoreOrder;
use Yunshop\Supplier\common\models\SupplierOrder;

class OrderBonusListeners
{
    use DispatchesJobs;
    protected $orderModel;

    public function subscribe(Dispatcher $events)
    {
        //下单
        $events->listen(AfterOrderCreatedEvent::class, self::class . '@addCount', 2);

        $events->listen(AfterOrderPaidEvent::class, self::class . '@orderPay');

        //收货之后 更改订单状态
        $events->listen(AfterOrderReceivedEvent::class, self::class . '@updateBonus');

        //订单取消
        $events->listen(AfterOrderCanceledEvent::class, self::class . '@cancel');

        //订单退款
        $events->listen(AfterOrderRefundedEvent::class, self::class . '@refunded');

    }

    public function orderPay(AfterOrderPaidEvent $event)
    {
        OrderIncomeCount::updateByOrderId($event->getOrderModel()->id, ['status' => $event->getOrderModel()->status]);
    }

    public function addCount(AfterOrderCreatedEvent $event)
    {
        $orderModel = $event->getOrderModel();
        $build = OrderIncomeCount::where('order_id',$orderModel->id)->first();
        if (!$build) {
            (new OrderCountContentJob($orderModel))->handle();
        }
    }

    public function updateBonus(AfterOrderReceivedEvent $event)
    {
        $orderModel = $event->getOrderModel();
        $this->dispatch(new OrderMemberMonthJob($orderModel));
        (new OrderBonusStatusJob($orderModel->id))->handle();
        $this->receivedBonus($orderModel);
    }
    
    public function receivedBonus($orderModel)
    {
        $data = [];

        if ($orderModel->is_plugin == 1 || $orderModel->plugin_id == 92) {
            $data['supplier'] = SupplierOrder::where('order_id', $orderModel->id)->sum('supplier_profit');
        }
        if ($orderModel->plugin_id == 31) {
            $data['cost_price'] = $data['cashier'] = CashierOrder::where('order_id', $orderModel->id)->sum('amount');
        }
        if ($orderModel->plugin_id == 32) {
            $data['cost_price'] = $data['store'] = StoreOrder::where('order_id', $orderModel->id)->sum('amount');
        }
        $data['status'] = $orderModel->status;
        OrderIncomeCount::updateByOrderId($orderModel->id, $data);
    }

    public function cancel(AfterOrderCanceledEvent $event)
    {
        OrderIncomeCount::updateByOrderId($event->getOrderModel()->id, ['status' => -1]);
    }

    public function refunded(AfterOrderRefundedEvent $event)
    {
        OrderIncomeCount::updateByOrderId($event->getOrderModel()->id, ['status' => -2]);
    }


}