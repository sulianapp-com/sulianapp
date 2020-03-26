<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzAlipayPayOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_alipay_pay_order')) {
            Schema::create('yz_alipay_pay_order', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable();
                $table->integer('order_id')->nullable();
                $table->integer('member_id')->nullable();
                $table->integer('account_id')->nullable();
                $table->string('pay_sn')->nullable();
                $table->string('order_sn')->nullable();
                $table->string('trade_no')->nullable();
                $table->decimal('total_amount', 14)->nullable();
                $table->boolean('royalty')->default(0)->nullable();
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
		Schema::drop('ims_yz_excel_recharge_detail');
	}

}
