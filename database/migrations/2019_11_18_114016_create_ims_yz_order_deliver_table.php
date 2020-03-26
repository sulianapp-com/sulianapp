<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzOrderDeliverTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_order_deliver')) {
            Schema::create('yz_order_deliver',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('order_id')->nullable();
                    $table->integer('deliver_id')->nullable();
                    $table->integer('clerk_id')->nullable();
                    $table->string('deliver_name', 255)->nullable();
                    $table->integer('created_at')
                        ->nullable();
                    $table->integer('updated_at')
                        ->nullable();
                    $table->integer('deleted_at')
                        ->nullable();
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
		Schema::drop('yz_order_deliver');
	}

}
