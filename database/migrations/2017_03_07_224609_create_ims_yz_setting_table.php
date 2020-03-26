<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzSettingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_setting')) {
            Schema::create('yz_setting', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid');
                $table->string('group')->default('shop');
                $table->string('key');
                $table->string('type');
                $table->text('value', 65535);
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
		Schema::dropIfExists('yz_setting');
	}

}
