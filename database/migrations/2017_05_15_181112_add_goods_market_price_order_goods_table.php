<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGoodsMarketPriceOrderGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_order_goods')) {
            Schema::table('yz_order_goods', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_order_goods', 'goods_market_price')) {

                    $table->decimal('goods_market_price', 10)->default(0.00);
                }
                if (!Schema::hasColumn('yz_order_goods', 'goods_cost_price')) {

                    $table->decimal('goods_cost_price', 10)->default(0.00);
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
        if (Schema::hasTable('yz_order_goods')) {
            Schema::table('yz_order_goods', function (Blueprint $table) {
                $table->dropColumn('goods_market_price');
                $table->dropColumn('goods_cost_price');
            });
        }
    }
}
