<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToYzBalanceRecharge extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_balance_recharge')) {
            Schema::table('yz_balance_recharge', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_balance_recharge', 'remark')) {
                    $table->string('remark', 50)->nullable();
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
