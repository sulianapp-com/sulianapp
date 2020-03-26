<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzBalanceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_balance')) {
            Schema::create('yz_balance', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable();
                $table->integer('member_id')->nullable();
                $table->decimal('old_money', 14)->nullable();
                $table->decimal('change_money', 14);
                $table->decimal('new_money', 14);
                $table->boolean('type');
                $table->boolean('service_type');
                $table->string('serial_number', 45)->default('');
                $table->integer('operator');
                $table->string('operator_id', 45)->default('');
                $table->string('remark', 200)->default('');
                $table->integer('created_at');
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
		Schema::dropIfExists('yz_balance');
	}

}
