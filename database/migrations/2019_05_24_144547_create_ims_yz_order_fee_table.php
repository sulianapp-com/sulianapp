<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzOrderFeeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!\Illuminate\Support\Facades\Schema::hasTable('yz_order_fee')) {

            \Illuminate\Support\Facades\Schema::create('yz_order_fee', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uid')->default(0);
                $table->integer('order_id');
                $table->string('fee_code', 50)->default('')->comment('手续费码');
                $table->string('name', 100)->default('')->comment('名称');
                $table->decimal('amount', 10)->default(0.00)->comment('金额');
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
        if (\Schema::hasTable('yz_order_fee')) {
            Schema::drop('yz_order_fee');
        }
	}

}
