<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMenuTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_menu')) {
            Schema::create('yz_menu', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('name', 45);
                $table->string('item', 45);
                $table->string('url')->default('');
                $table->string('url_params')->default('');
                $table->boolean('permit')->default(0);
                $table->boolean('menu')->default(0);
                $table->string('icon', 45)->default('');
                $table->integer('parent_id')->default(0);
                $table->integer('sort')->default(0);
                $table->boolean('status')->default(0);
                $table->integer('created_at')->default(0);
                $table->integer('updated_at')->default(0);
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
		Schema::dropIfExists('yz_menu');
	}

}
