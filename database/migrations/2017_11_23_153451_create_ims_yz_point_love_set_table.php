<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPointLoveSetTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_point_love_set')) {
            Schema::create('yz_point_love_set', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->comment('所属公众号');
                $table->integer('member_id');
                $table->string('rate')->default('');
                $table->integer('created_at');
                $table->string('updated_at', 45);
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
		Schema::dropIfExists('yz_point_love_set');
	}

}
