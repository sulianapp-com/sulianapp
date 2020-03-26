<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDefaultDiscountValueToImsYzGoodsDiscountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Illuminate\Support\Facades\Schema::hasTable('yz_goods_discount')) {
            \Illuminate\Support\Facades\Schema::table('yz_goods_discount',
                function (Blueprint $table) {
                    if (\Illuminate\Support\Facades\Schema::hasColumn('yz_goods_discount', 'discount_value')) {
                        $table->string('discount_value', 10)->nullable()->change();
                    }
                });
            \Illuminate\Support\Facades\DB::table('yz_goods_discount')->where('discount_value', 0)->update(['discount_value' => '']);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
