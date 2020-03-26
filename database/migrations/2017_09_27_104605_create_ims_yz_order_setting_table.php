<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzOrderSettingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_order_setting')) {
            Schema::create('yz_order_setting', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('order_id')->default(0);
                $table->string('key', 50)->default('');
                $table->text('value', 65535);
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
        if (Schema::hasTable('yz_order_setting')) {
            Schema::drop('yz_order_setting');
        }
	}

}
