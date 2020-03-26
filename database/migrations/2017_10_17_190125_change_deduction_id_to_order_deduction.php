<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDeductionIdToOrderDeduction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_order_deduction')) {

            if (\Schema::hasColumn('yz_order_deduction', 'deduction_id')) {
                \Schema::table('yz_order_deduction', function ($table) {

                    $table->renameColumn('deduction_id', 'code');
                    $table->renameColumn('qty', 'coin');

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
