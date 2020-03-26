<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberQqTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_member_qq')) {
            Schema::create('yz_member_qq', function (Blueprint $table) {
                $table->integer('qq_id', true);
                $table->integer('uniacid');
                $table->integer('member_id');
                $table->string('nickname');
                $table->string('figureurl');
                $table->string('figureurl_1');
                $table->string('figureurl_2');
                $table->string('figureurl_qq_1');
                $table->string('figureurl_qq_2');
                $table->boolean('gender')->default(0);
                $table->string('is_yellow_year_vip', 45)->default('0');
                $table->integer('vip')->default(0);
                $table->boolean('yellow_vip_level')->default(0);
                $table->boolean('level')->default(0);
                $table->boolean('is_yellow_vip')->default(0);
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
		Schema::dropIfExists('yz_member_qq');
	}

}
