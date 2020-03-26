<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCodeToOrderDeduction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_order_deduction')) {

            if (\Schema::hasColumn('yz_order_deduction', 'code')) {
                \Schema::table('yz_order_deduction', function ($table) {
                    
                    $table->string('code', 50)->default('')->change();
                    $table->decimal('coin', 10)->default(0.00)->change();
                });

            }
            // id改为对应code
            $orderDeductions = \app\common\models\order\OrderDeduction::get();


            $orderDeductions->each(function ($orderDeductions) {
                if ($orderDeductions->code == 1) {
                    $orderDeductions->code = 'point';
                } elseif ($orderDeductions->code == 2) {
                    $orderDeductions->code = 'love';
                } elseif ($orderDeductions->code == 3) {
                    $orderDeductions->code = 'coin';
                }
                $orderDeductions->save();
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
