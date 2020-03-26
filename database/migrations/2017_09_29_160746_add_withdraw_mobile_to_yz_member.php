<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWithdrawMobileToYzMember extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Schema::hasTable('yz_member')) {

            if (!Schema::hasColumn('yz_member', 'withdraw_mobile')) {
                Schema::table('yz_member', function ($table) {
                    $table->string('withdraw_mobile','11')->nullable()->default('');
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
        Schema::table('yz_member', function (Blueprint $table) {
            $table->dropColumn('withdraw_mobile');
        });
    }
}
