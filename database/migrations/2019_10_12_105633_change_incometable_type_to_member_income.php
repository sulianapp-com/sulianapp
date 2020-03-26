<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeIncometableTypeToMemberIncome extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_member_income')) {
            if (Schema::hasColumn('yz_member_income', 'incometable_type')) {
                Schema::table('yz_member_income', function (Blueprint $table) {
                    $table->string('incometable_type', 100)->change();
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
