<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzUserRoleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_user_role')) {
            Schema::create('yz_user_role', function (Blueprint $table) {
                $table->integer('user_id');
                $table->integer('role_id');
                $table->primary(['user_id', 'role_id']);
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
		Schema::dropIfExists('yz_user_role');
	}

}
