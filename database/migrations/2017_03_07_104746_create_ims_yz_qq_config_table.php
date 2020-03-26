<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzQqConfigTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_qq_config')) {
            Schema::create('yz_qq_config', function (Blueprint $table) {
                $table->integer('config_id')->primary();
                $table->integer('uniacid')->default(0);
                $table->string('app_key', 64);
                $table->string('app_secret', 64);
                $table->boolean('type')->default(0);
                $table->integer('created_at')->unsigned()->default(0);
                $table->integer('updated_at')->unsigned()->default(0);
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
		Schema::dropIfExists('yz_qq_config');
	}

}
