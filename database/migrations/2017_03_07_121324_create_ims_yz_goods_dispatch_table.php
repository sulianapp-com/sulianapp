<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzGoodsDispatchTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_goods_dispatch')) {
            Schema::create('yz_goods_dispatch', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('goods_id')->index('idx_good_id');
                $table->boolean('dispatch_type')->default(1);
                $table->integer('dispatch_price')->nullable()->default(0);
                $table->integer('dispatch_id')->nullable();
                $table->boolean('is_cod')->default(1);
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
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
		Schema::dropIfExists('yz_goods_dispatch');
	}

}
