<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentAmountToOrderGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_order_goods')) {
            Schema::table('yz_order_goods',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_order_goods', 'payment_amount')) {
                        $table->decimal('payment_amount', 10)->default(0.00);
                    }
                    if (!Schema::hasColumn('yz_order_goods', 'deduction_amount')) {
                        $table->decimal('deduction_amount', 10)->default(0.00);
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
            if (Schema::hasColumn('yz_order_goods', 'payment_amount')) {
                Schema::table('yz_order_goods', function (Blueprint $table) {
                    $table->dropColumn('payment_amount');
                });
            }
            if (Schema::hasColumn('yz_order_goods', 'deduction_amount')) {

                Schema::table('yz_order_goods', function (Blueprint $table) {
                    $table->dropColumn('deduction_amount');
                });
            }
        }
    }
}
