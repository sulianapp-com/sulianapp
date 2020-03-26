<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzExhelperGoodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_exhelper_goods')) {
            Schema::create('yz_exhelper_goods', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('goods_id')->default(0);
                $table->string('short_title', 100)->nullable()->default('');
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
		Schema::drop('ims_yz_exhelper_goods');
	}

}
