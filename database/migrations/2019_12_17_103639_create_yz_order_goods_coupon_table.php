<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use app\common\facades\Setting;
use app\common\models\UniAccount;

class CreateYzOrderGoodsCouponTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_order_goods_coupon')) {
            Schema::create('yz_order_goods_coupon', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid');
                $table->integer('order_goods_id');
                $table->integer('coupon_id');
                $table->integer('coupon_several')->default(1);
                $table->tinyInteger('send_type')->default(1);
                $table->integer('send_num')->default(0)->comment('发放数量。字段名和原流程一致');
                $table->integer('end_send_num')->default(0)->comment('已发放。字段名和原流程一致');
                $table->boolean('status')->default(0);
                $table->string('remark')->nullable();
                $table->string('num_reason')->nullable()->comment('应发几张和实发几张不一致时原因');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->unique(['order_goods_id', 'coupon_id'], 'order_goods_id_coupon_id');
            });
        }
        if(Schema::hasTable('yz_order_goods_coupon'))
        {
            $uniAccount = UniAccount::get() ?: [];
            foreach ($uniAccount as $u) {
                Setting::$uniqueAccountId = \YunShop::app()->uniacid = $u->uniacid;
                $orders = \app\common\models\Order::uniacid()
                    ->whereIn('status',[\app\common\models\Order::WAIT_PAY,\app\common\models\Order::WAIT_SEND,\app\common\models\Order::WAIT_RECEIVE])
                    ->get();
                if(!$orders->isEmpty())
                {
                    foreach ($orders as $order)
                    {
                        $orderGoods = $order->hasManyOrderGoods;//订单商品
                        $couponService = new \app\frontend\modules\coupon\services\CouponService($order, null, $orderGoods);
                        $couponService->sendCouponLog();
                    }
                }
            }
        }
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
