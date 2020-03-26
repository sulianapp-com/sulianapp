<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzExhelperPrintTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_exhelper_print')) {
            Schema::create('yz_exhelper_print', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('order_id')->default(0);
                $table->boolean('express_print_status')->default(0);
                $table->boolean('send_print_status')->default(0);
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
		Schema::drop('ims_yz_exhelper_print');
	}

}
