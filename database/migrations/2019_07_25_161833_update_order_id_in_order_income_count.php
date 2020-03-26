<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrderIdInOrderIncomeCount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_order_income_count')) {
            if (Schema::hasColumn('yz_order_income_count', 'order_id')) {
                Schema::table('yz_order_income_count', function (Blueprint $table) {
                    $table->integer('order_id')->change();
                    $table->index('order_id');
                    $table->index('uniacid');
                    $table->index('uid');
                    $table->index('status');
                });
            }
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
