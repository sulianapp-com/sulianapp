<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewDataToYzMembershipInfomattionLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (Schema::hasTable('yz_membership_infomattion_log')) {
            Schema::table('yz_membership_infomattion_log', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_membership_infomattion_log', 'new_data')) {
                    $table->string('new_data')->nullable()->comment('用户修改后信息');
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
        Schema::table('yz_membership_infomattion_log', function (Blueprint $table) {
            //
        });
    }
}
