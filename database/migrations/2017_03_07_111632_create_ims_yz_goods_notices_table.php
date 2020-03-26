<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzGoodsNoticesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_goods_notices')) {
            Schema::create('yz_goods_notices', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('goods_id')->index('idx_good_id');
                $table->integer('uid')->nullable();
                $table->boolean('type')->nullable();
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
		Schema::dropIfExists('yz_goods_notices');
	}

}
