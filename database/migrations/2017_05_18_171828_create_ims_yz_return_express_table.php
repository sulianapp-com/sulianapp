<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzReturnExpressTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_return_express')) {
            Schema::create('yz_return_express', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('refund_id')->default(0)->index('idx_order_id');
                $table->string('express_company_name', 50)->default('0');
                $table->string('express_sn', 50)->default('0');
                $table->string('express_code', 20)->default('0');
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
		Schema::dropIfExists('yz_return_express');
	}

}
