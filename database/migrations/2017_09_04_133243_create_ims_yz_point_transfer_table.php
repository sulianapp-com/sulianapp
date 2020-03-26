<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPointTransferTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!\Schema::hasTable('yz_point_transfer')) {
            Schema::create('yz_point_transfer', function (Blueprint $table) {
                $table->integer('id', true)->comment('主键');
                $table->integer('uniacid')->nullable()->comment('公众号ID');
                $table->integer('transferor')->nullable()->comment('转让者');
                $table->integer('recipient')->nullable()->comment('被转让者');
                $table->decimal('value', 14)->nullable()->comment('转让值');
                $table->integer('created_at')->nullable()->comment('创建时间');
                $table->boolean('status')->nullable()->comment('-1失败，1成功');
                $table->integer('updated_at');
                $table->string('order_sn', 45);
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
        if (\Schema::hasTable('yz_point_transfer')) {
            Schema::drop('yz_point_transfer');
        }
	}

}
