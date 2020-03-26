<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use app\common\models\Order;
use app\common\models\OrderPay;
use Illuminate\Database\Migrations\Migration;

class FixOrderPayId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $r = Order::where('pay_time','>',0)->where(function ($query){
            return $query->wherePayTypeId(0)->orWhere('order_pay_id',0);
        })->get();
        $r->each(function($order){

            $orderPay = OrderPay::where(['order_ids'=>'["'.$order->id.'"]'])->first();

            if(isset($orderPay)){
                $order->pay_type_id = $orderPay->pay_type_id;
                $order->order_pay_id = $orderPay->id;
                $order->save();
            }

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
