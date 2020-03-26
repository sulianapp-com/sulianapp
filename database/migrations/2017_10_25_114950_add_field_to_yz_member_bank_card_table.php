<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToYzMemberBankCardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Schema::hasTable('yz_member_bank_card')) {

            if (!Schema::hasColumn('yz_member_bank_card', 'bank_province')) {
                Schema::table('yz_member_bank_card', function ($table) {
                    $table->string('bank_province','45');
                });
            }

            if (!Schema::hasColumn('yz_member_bank_card', 'bank_city')) {
                Schema::table('yz_member_bank_card', function ($table) {
                    $table->string('bank_city','45');
                });
            }

            if (!Schema::hasColumn('yz_member_bank_card', 'bank_branch')) {
                Schema::table('yz_member_bank_card', function ($table) {
                    $table->string('bank_branch','45');
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
