<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPermissionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_permission')) {
            Schema::create('yz_permission', function (Blueprint $table) {
                $table->increments('id');
                $table->boolean('type');
                $table->integer('item_id');
                $table->string('permission');
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
		Schema::dropIfExists('yz_permission');
	}

}
