<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberWechatTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_member_wechat')) {
            Schema::create('yz_member_wechat', function (Blueprint $table) {
                $table->integer('wechat_id', true);
                $table->integer('uniacid');
                $table->integer('member_id')->index('idx_member_id');
                $table->string('openid', 50);
                $table->string('nickname', 20);
                $table->boolean('gender')->default(0);
                $table->string('avatar');
                $table->string('province', 4);
                $table->string('city', 25);
                $table->string('country', 10);
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
		Schema::dropIfExists('yz_member_wechat');
	}

}
