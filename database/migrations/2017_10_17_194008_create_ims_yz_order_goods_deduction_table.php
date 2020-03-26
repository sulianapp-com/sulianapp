<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzOrderGoodsDeductionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_order_goods_deduction')) {

            Schema::create('yz_order_goods_deduction', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uid')->default(0);
                $table->integer('order_id');
                $table->integer('order_goods_id')->nullable();
                $table->string('code', 50)->default('');
                $table->string('name', 100)->default('')->comment('抵扣名称');
                $table->decimal('usable_amount', 10)->default(0.00)->comment('抵扣金额');
                $table->decimal('usable_coin', 10)->default(0.00)->comment('抵扣数值');
                $table->integer('updated_at')->nullable();
                $table->integer('created_at')->nullable();
                $table->integer('deleted_at')->nullable();
                $table->decimal('used_amount', 10)->default(0.00)->comment('抵扣数值');
                $table->decimal('used_coin', 10)->default(0.00)->comment('抵扣金额');
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
        if (Schema::hasTable('yz_order_goods_deduction')) {

            Schema::drop('yz_order_goods_deduction');
        }
	}

}
