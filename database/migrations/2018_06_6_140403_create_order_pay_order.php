<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderPayOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_order_pay_order')) {
            Schema::create('yz_order_pay_order', function(Blueprint $table) {
                $table->integer('id', true);
                $table->integer('order_id')->index('idx_order_id');
                $table->integer('order_pay_id')->index('idx_order_pay_id');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
            });
            \app\common\models\OrderPay::get()->each(function(\app\common\models\OrderPay $orderPay){
                foreach ($orderPay->order_ids as $order_id){

                    $a= new \app\common\models\OrderPayOrder([
                        'order_pay_id'=>$orderPay->id,
                        'order_id'=>$order_id
                    ]);
                    $a->save();
                }
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('yz_order_pay_order')) {
            Schema::dropIfExists('yz_order_pay_order');

        }
    }
}
