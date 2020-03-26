<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPayRefundOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_pay_refund_order')) {
            Schema::create('yz_pay_refund_order', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->integer('uniacid');
                $table->integer('member_id');
                $table->string('int_order_no', 32);
                $table->string('out_order_no', 32);
                $table->string('trade_no', 255);
                $table->decimal('price', 14,2);
                $table->string('type', 255);
                $table->tinyInteger('status');
                $table->integer('created_at')->default(0);
                $table->integer('updated_at')->default(0);
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
		Schema::dropIfExists('yz_pay_refund_order');
	}

}
