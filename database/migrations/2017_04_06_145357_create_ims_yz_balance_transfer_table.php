<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzBalanceTransferTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_balance_transfer')) {
            Schema::create('yz_balance_transfer', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable();
                $table->integer('transferor')->nullable();
                $table->integer('recipient')->nullable();
                $table->decimal('money', 14)->nullable();
                $table->integer('created_at')->nullable();
                $table->boolean('status')->nullable();
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
		Schema::dropIfExists('yz_balance_transfer');
	}

}
