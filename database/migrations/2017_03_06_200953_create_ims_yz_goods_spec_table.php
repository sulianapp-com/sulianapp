<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzGoodsSpecTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_goods_spec')) {
            Schema::create('yz_goods_spec', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid');
                $table->integer('goods_id')->nullable()->default(0)->index('idx_goodsid');
                $table->string('title', 50)->nullable();
                $table->string('description', 1000)->nullable();
                $table->boolean('display_type')->nullable()->default(0);
                $table->text('content', 65535)->nullable();
                $table->integer('display_order')->nullable()->default(0)->index('idx_displayorder');
                $table->string('propId')->nullable();
                $table->integer('created_at')->nullable();
                $table->integer('deleted_at')->nullable();
                $table->integer('updated_at')->nullable();
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
		Schema::dropIfExists('yz_goods_spec');
	}

}
