<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzBalanceRechargeActivityTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_balance_recharge_activity')) {
            Schema::create('yz_balance_recharge_activity', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid');
                $table->integer('member_id');
                $table->integer('activity_id');
                $table->integer('partake_count');
                $table->integer('created_at');
                $table->integer('updated_at');
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
		Schema::dropIfExists('yz_balance_recharge_activity');
	}

}
