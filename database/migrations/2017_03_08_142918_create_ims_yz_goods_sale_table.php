<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzGoodsSaleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_goods_sale')) {
            Schema::create('yz_goods_sale', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('goods_id')->index('idx_good_id');
                $table->string('max_point_deduct', 255)->nullable();
                $table->integer('max_balance_deduct')->nullable()->default(0);
                $table->integer('is_sendfree')->nullable()->default(0);
                $table->integer('ed_num')->nullable()->default(0);
                $table->integer('ed_money')->nullable();
                $table->text('ed_areas', 65535)->nullable();
                $table->string('point', 255)->nullable();
                $table->integer('bonus')->nullable()->default(0);
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
		Schema::dropIfExists('yz_goods_sale');
	}

}
