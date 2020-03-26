<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberBankCardTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_member_bank_card')) {
            Schema::create('yz_member_bank_card', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid');
                $table->integer('member_id');
                $table->string('member_name', 45)->default('');
                $table->string('bank_name', 45)->default('');
                $table->string('bank_card', 100)->default('');
                $table->boolean('is_default');
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
		Schema::drop('yz_member_bank_card');
	}

}
