<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzGoodsCouponQueueTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_goods_coupon_queue')) {
            Schema::create('yz_goods_coupon_queue', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->nullable();
                $table->integer('goods_id')->nullable()->comment('商品ID');
                $table->integer('uid')->nullable()->comment('会员ID');
                $table->integer('coupon_id')->nullable()->comment('优惠券ID');
                $table->integer('send_num')->nullable()->comment('发放数量');
                $table->integer('end_send_num')->nullable()->comment('已发放');
                $table->integer('status')->nullable()->comment('状态 0 ：未完成 1：已完成');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
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
        if (Schema::hasTable('yz_goods_coupon_queue')) {
            Schema::drop('yz_goods_coupon_queue');
        }
	}

}
