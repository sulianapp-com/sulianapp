<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTeamPerformanceToYzMemberLevelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_member_level')) {
            Schema::table('yz_member_level', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_member_level', 'team_performance')) {
                    $table->decimal('team_performance', 10)->nullable()->comment('团队业绩');
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
