<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddImsYzGoodsPrivilegeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('yz_goods_privilege')) {
            Schema::table('yz_goods_privilege',
                function (Blueprint $table) {

                if (!Schema::hasColumn('yz_goods_privilege', 'day_buy_limit')) {
                    $table->integer('day_buy_limit')->nullable()->default(0);
                }

                if (!Schema::hasColumn('yz_goods_privilege', 'week_buy_limit')) {
                    $table->integer('week_buy_limit')->nullable()->default(0);
                }

                if (!Schema::hasColumn('yz_goods_privilege', 'month_buy_limit')) {
                    $table->integer('month_buy_limit')->nullable()->default(0);
                }

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
