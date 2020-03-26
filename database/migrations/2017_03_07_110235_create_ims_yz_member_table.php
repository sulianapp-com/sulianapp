<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_member')) {
            Schema::create('yz_member', function (Blueprint $table) {
                $table->integer('member_id')->index('idx_member_id');
                $table->integer('uniacid')->index('idx_uniacid');
                $table->integer('parent_id')->nullable();
                $table->integer('group_id')->default(0);
                $table->integer('level_id')->default(0);
                $table->integer('inviter')->nullable()->default(0);
                $table->boolean('is_black')->default(0);
                $table->string('province_name', 15)->nullable();
                $table->string('city_name', 15)->nullable();
                $table->string('area_name', 15)->nullable();
                $table->integer('province')->nullable();
                $table->integer('city')->nullable();
                $table->integer('area')->nullable();
                $table->text('address', 65535)->nullable();
                $table->string('referralsn', 255)->nullable();
                $table->boolean('is_agent')->nullable();
                $table->string('alipayname')->nullable();
                $table->string('alipay')->nullable();
                $table->text('content', 65535)->nullable();
                $table->integer('status')->nullable()->default(0);
                $table->integer('child_time')->nullable()->default(0);
                $table->integer('agent_time')->nullable()->default(0);
                $table->integer('apply_time')->nullable()->default(0);
                $table->string('relation', 255)->nullable();
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
		Schema::dropIfExists('yz_member');
	}

}
