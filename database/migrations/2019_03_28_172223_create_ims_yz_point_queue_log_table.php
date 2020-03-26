<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPointQueueLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_point_queue_log')) {
            Schema::create('yz_point_queue_log', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid');
                $table->integer('uid')->comment('会员ID');
                $table->integer('queue_id')->comment('积分队列ID');
                $table->decimal('amount', 14)->comment('赠送数量');
                $table->decimal('point_total',
                    10)->default(0.00)->comment('赠送积分总数');
                $table->decimal('finish_point',
                    10)->default(0.00)->comment('完成数量');
                $table->decimal('surplus_point',
                    10)->default(0.00)->comment('剩余数量');
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
		Schema::drop('ims_yz_point_queue_log');
	}

}
