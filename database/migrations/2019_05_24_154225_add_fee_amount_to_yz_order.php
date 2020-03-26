<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFeeAmountToYzOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_order')) {
            if (!Schema::hasColumn('yz_order', 'fee_amount')) {
                Schema::table('yz_order', function (Blueprint $table) {
                    $table->decimal('fee_amount', 14)->default(0.00);
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
