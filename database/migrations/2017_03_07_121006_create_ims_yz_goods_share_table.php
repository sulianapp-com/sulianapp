<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzGoodsShareTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_goods_share')) {
            Schema::create('yz_goods_share', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('goods_id')->index('idx_goodid');
                $table->boolean('need_follow')->nullable();
                $table->string('no_follow_message')->nullable()->default('');
                $table->string('follow_message')->nullable()->default('');
                $table->string('share_title', 50)->nullable()->default('');
                $table->string('share_thumb')->nullable()->default('');
                $table->string('share_desc')->nullable()->default('');
                $table->integer('created_at');
                $table->integer('updated_at');
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
		Schema::dropIfExists('yz_goods_share');
	}

}
