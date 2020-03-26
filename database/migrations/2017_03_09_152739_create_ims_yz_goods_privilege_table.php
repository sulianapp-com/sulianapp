<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzGoodsPrivilegeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_goods_privilege')) {
            Schema::create('yz_goods_privilege', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('goods_id')->index('idx_goodid');
                $table->text('show_levels', 65535)->nullable();
                $table->text('show_groups', 65535)->nullable();
                $table->text('buy_levels', 65535)->nullable();
                $table->text('buy_groups', 65535)->nullable();
                $table->integer('once_buy_limit')->nullable()->default(0);
                $table->integer('total_buy_limit')->nullable()->default(0);
                $table->integer('time_begin_limit')->nullable();
                $table->integer('time_end_limit')->nullable();
                $table->boolean('enable_time_limit');
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
		Schema::dropIfExists('yz_goods_privilege');
	}

}
