<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPointQueueTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_point_queue')) {
            Schema::create('yz_point_queue', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid');
                $table->integer('uid')->comment('会员ID');
                $table->integer('order_id')->nullable();
                $table->integer('goods_id')->nullable();
                $table->decimal('point_total',
                    10)->default(0.00)->comment('赠送积分总数');
                $table->decimal('finish_point',
                    10)->default(0.00)->comment('完成数量');
                $table->decimal('surplus_point',
                    10)->default(0.00)->comment('剩余数量');
                $table->decimal('once_unit',
                    10)->default(0.00)->comment('每次赠送数量');
                $table->decimal('last_point',
                    10)->default(0.00)->comment('最后一次赠送数量');
                $table->boolean('status')->nullable()->default(0)->comment('1已完成');
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
        Schema::drop('ims_yz_point_queue');
    }
}
