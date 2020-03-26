<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzGoodsSpecItemTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_goods_spec_item')) {
            Schema::create('yz_goods_spec_item', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid');
                $table->integer('specid')->nullable()->default(0)->index('idx_specid');
                $table->string('title')->nullable();
                $table->string('thumb')->nullable();
                $table->integer('show')->nullable()->default(0)->index('idx_show');
                $table->integer('display_order')->nullable()->default(0)->index('idx_displayorder');
                $table->string('valueId')->nullable();
                $table->integer('virtual')->nullable()->default(0);
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
		Schema::dropIfExists('yz_goods_spec_item');
	}

}
