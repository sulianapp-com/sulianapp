<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzExhelperOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_exhelper_order')) {
            Schema::create('yz_exhelper_order', function (Blueprint $table) {
                $table->increments('id');
                $table->string('order_sn')->default('');
                $table->string('realname', 100)->nullable();
                $table->string('mobile', 50)->nullable();
                $table->string('address')->nullable();
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
		Schema::drop('ims_yz_exhelper_order');
	}

}
