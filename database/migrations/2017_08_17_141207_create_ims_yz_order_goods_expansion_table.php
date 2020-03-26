<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzOrderGoodsExpansionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!\Schema::hasTable('yz_order_goods_expansion')) {
            Schema::create('yz_order_goods_expansion', function (Blueprint $table) {
                $table->increments('id');
                $table->string('key')->default('');
                $table->string('value')->default('');
                $table->integer('order_goods_id');
                $table->string('plugin_code', 50);
                $table->integer('updated_at')->nullable();
                $table->integer('created_at')->nullable();
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
        if (\Schema::hasTable('yz_order_goods_expansion')) {
            Schema::drop('yz_order_goods_expansion');
        }
	}

}
