<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzExhelperSenduserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_exhelper_senduser')) {
            Schema::create('yz_exhelper_senduser', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0);
                $table->string('sender_name')->nullable();
                $table->string('sender_tel')->nullable();
                $table->string('sender_sign')->nullable();
                $table->integer('sender_code')->nullable();
                $table->string('sender_address')->nullable();
                $table->string('sender_city')->nullable();
                $table->boolean('isdefault')->default(0);
                $table->integer('uid')->default(0);
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
		Schema::drop('ims_yz_exhelper_senduser');
	}

}
