<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzWithdrawTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_withdraw')) {
            Schema::create('yz_withdraw', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->nullable();
                $table->integer('member_id')->nullable();
                $table->string('type', 60)->nullable();
                $table->string('type_id', 60)->nullable();
                $table->decimal('amounts', 14)->nullable();
                $table->decimal('poundage', 14)->nullable();
                $table->decimal('poundage_rate')->nullable();
                $table->string('pay_way', 100)->nullable();
                $table->boolean('status')->nullable();
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
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
		Schema::dropIfExists('yz_withdraw');
	}

}
