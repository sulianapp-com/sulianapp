<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldManualTypeToYzWithdrawTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_withdraw')) {
            Schema::table('yz_withdraw',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_withdraw', 'manual_type')) {
                        $table->boolean('manual_type')->default(0);
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
