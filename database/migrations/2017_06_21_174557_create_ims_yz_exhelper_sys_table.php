<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzExhelperSysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_exhelper_sys')) {
            Schema::create('yz_exhelper_sys', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->nullable()->default(0);
                $table->string('ip', 20)->nullable()->default('');
                $table->integer('port')->nullable()->default(0);
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
		Schema::drop('ims_yz_exhelper_sys');
	}

}
