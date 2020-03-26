<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToImsYzOrderGoodsCoinExchangeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_order_goods_coin_exchange')) {
            Schema::table('yz_order_goods_coin_exchange',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_order_goods_coin_exchange', 'name')) {
                        $table->string('name')->nullable();
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
        Schema::table('yz_order_goods_coin_exchange', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
}
