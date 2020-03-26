<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberRelationStatisticsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('yz_member_relation_statistics')) {
			Schema::create('yz_member_relation_statistics', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('uniacid')->default(0);
				$table->integer('member_id')->default(0)->comment('会员ID');
				$table->integer('first_total')->default(0)->comment('一级下线总数');
				$table->integer('second_total')->default(0)->comment('二级下线总数');
				$table->integer('third_total')->default(0)->comment('三级下线总数');
				$table->integer('team_total')->default(0)->comment('团队总人数');
				$table->integer('created_at')->nullable();
				$table->integer('updated_at')->nullable();
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
		Schema::drop('yz_member_relation_statistics');
	}

}
