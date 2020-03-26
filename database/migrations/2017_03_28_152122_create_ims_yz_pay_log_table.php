<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPayLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_pay_log')) {
            Schema::create('yz_pay_log', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid');
                $table->integer('member_id');
                $table->tinyInteger('type');
                $table->string('third_type', 255);
                $table->decimal('price', 14, 2);
                $table->text('operation', 65535);
                $table->string('ip', 135);
                $table->integer('created_at')->default(0);
                $table->integer('updated_at')->nullable()->default(0);
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
		Schema::dropIfExists('yz_pay_log');
	}

}
