<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVipPriceToOrderGoods extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Schema::hasTable('yz_order_goods')) {
            Schema::table('yz_order_goods', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_order_goods', 'vip_price')) {
                    $table->decimal('vip_price', 10)->nullable()->default(0.00);
                    $table->decimal('coupon_price', 10)->nullable()->default(0.00);
                    //$table->decimal('vip_price', 10)->nullable()->default(0.00);
                }
            });
        }
        if (\Schema::hasTable('yz_order_refund')) {
            Schema::table('yz_order_refund', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_order_refund', 'reject_reason')) {
                    $table->longText('reject_reason');
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
        //
    }
}
