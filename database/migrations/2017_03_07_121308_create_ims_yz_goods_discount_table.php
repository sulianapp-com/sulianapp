<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzGoodsDiscountTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_goods_discount')) {
            Schema::create('yz_goods_discount', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('goods_id')->index('idx_goodid');
                $table->boolean('level_discount_type');
                $table->boolean('discount_method');
                $table->integer('level_id');
                $table->decimal('discount_value', 3);
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
		Schema::dropIfExists('yz_goods_discount');
	}

}
