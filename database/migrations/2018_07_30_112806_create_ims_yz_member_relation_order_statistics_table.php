<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberRelationOrderStatisticsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('yz_member_relation_order_statistics')) {
			Schema::create('yz_member_relation_order_statistics', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('uniacid')->default(0);
				$table->integer('member_id')->default(0)->comment('会员ID');
				$table->integer('first_order_quantity')->default(0)->comment('一级下线订单总数');
                $table->decimal('first_order_amount', 10)->nullable()->default(0.00)->comment('一级下线订单总额');
				$table->integer('second_order_quantity')->default(0)->comment('二级下线订单总数');
                $table->decimal('second_order_amount', 10)->nullable()->default(0.00)->comment('二级下线订单总额');
				$table->integer('third_order_quantity')->default(0)->comment('三级下线订单总数');
                $table->decimal('third_order_amount', 10)->nullable()->default(0.00)->comment('三级下线订单总额');
//				$table->integer('first_scened_order_quantity')->default(0)->comment('一、二级下线订单总数');
//                $table->decimal('first_scened_order_amount', 10)->nullable()->default(0.00)->comment('一、二级下线订单总额');
//                $table->integer('first_scened_third_order_quantity')->default(0)->comment('一、二、三级下线订单总数');
//                $table->decimal('first_scened_third_order_amount', 10)->nullable()->default(0.00)->comment('一、二、三级下线订单总额');
				$table->integer('team_order_quantity')->default(0)->comment('团队订单总数');
                $table->decimal('team_order_amount', 10)->nullable()->default(0.00)->comment('团队订单总额');
				$table->integer('created_at')->nullable();
				$table->integer('updated_at')->nullable();
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
		Schema::drop('yz_member_relation_order_statistics');
	}

}
