<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzOrderCouponTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!\Schema::hasTable('yz_order_coupon')) {

            Schema::create('yz_order_coupon', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uid')->default(0);
                $table->integer('order_id');
                $table->integer('coupon_id');
                $table->integer('member_coupon_id')->default(0);
                $table->string('name', 100)->default('');
                $table->decimal('amount', 10)->default(0.00);
                $table->integer('updated_at')->nullable();
                $table->integer('created_at')->nullable();
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
        if (\Schema::hasTable('yz_order_coupon')) {

            Schema::drop('yz_order_coupon');
        }
	}

}
