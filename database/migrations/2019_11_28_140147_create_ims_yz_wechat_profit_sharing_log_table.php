<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzWechatProfitSharingLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_wechat_profit_sharing_log')) {
            Schema::create('yz_wechat_profit_sharing_log', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid');
                $table->integer('order_id');
                $table->string('mch_id');
                $table->string('sub_mch_id');
                $table->string('appid');
                $table->string('sub_appid');
                $table->integer('type');
                $table->integer('account');
                $table->string('transaction_id');
                $table->string('out_order_no');
                $table->string('description');
                $table->integer('amount');
                $table->integer('status');
                $table->string('message');
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
