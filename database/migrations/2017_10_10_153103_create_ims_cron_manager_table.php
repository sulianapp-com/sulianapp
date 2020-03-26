<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsCronManagerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('cron_manager')) {
            Schema::create('cron_manager', function (Blueprint $table) {
                $table->increments('id');
                $table->dateTime('rundate');
                $table->float('runtime');
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
		Schema::drop('ims_cron_manager');
	}

}
