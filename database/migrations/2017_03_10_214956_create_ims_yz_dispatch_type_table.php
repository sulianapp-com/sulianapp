<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzDispatchTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_dispatch_type')) {
            Schema::create('yz_dispatch_type', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 50)->default('');
                $table->integer('plugin');
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
		Schema::dropIfExists('yz_dispatch_type');
	}

}
