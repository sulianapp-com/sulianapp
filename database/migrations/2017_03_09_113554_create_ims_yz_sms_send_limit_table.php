<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzSmsSendLimitTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_sms_send_limit')) {
            Schema::create('yz_sms_send_limit', function (Blueprint $table) {
                $table->integer('sms_id', true);
                $table->integer('uniacid');
                $table->string('mobile', 11);
                $table->boolean('total');
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
		Schema::dropIfExists('yz_sms_send_limit');
	}

}
