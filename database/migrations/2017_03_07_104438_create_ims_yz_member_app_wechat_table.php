<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberAppWechatTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_member_wechat')) {
            Schema::create('yz_member_app_wechat', function (Blueprint $table) {
                $table->integer('app_wechat_id')->primary();
                $table->integer('uniacid');
                $table->integer('member_id');
                $table->string('openid', 50);
                $table->string('nickname', 20);
                $table->string('avatar');
                $table->boolean('gender')->default(0);
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
		Schema::dropIfExists('yz_member_app_wechat');
	}

}
