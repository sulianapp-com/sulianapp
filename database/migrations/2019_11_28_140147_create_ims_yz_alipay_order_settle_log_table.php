<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzAlipayOrderSettleLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_alipay_order_settle_log')) {
            Schema::create('yz_alipay_order_settle_log', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable();
                $table->integer('order_id')->nullable();
                $table->string('app_id')->nullable();
                $table->string('app_auth_token')->nullable();
                $table->string('royalty_type')->nullable();
                $table->string('trans_out_type')->nullable();
                $table->string('trans_in_type')->nullable();
                $table->string('trans_out')->nullable();
                $table->string('trans_in')->nullable();
                $table->string('trade_no')->nullable();
                $table->string('out_request_no')->nullable();
                $table->string('message')->nullable();
                $table->integer('amount')->nullable();
                $table->integer('status')->nullable();
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
