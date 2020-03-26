<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddYzMemberRelationRewardPoints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_member_relation')) {
            if (!Schema::hasColumn('yz_member_relation', 'reward_points')) {
                Schema::table('yz_member_relation', function (Blueprint $table) {
                    $table->integer('reward_points')->default(0);
                });
            }
            if (!Schema::hasColumn('yz_member_relation', 'maximum_number')) {
                Schema::table('yz_member_relation', function (Blueprint $table) {
                    $table->integer('maximum_number')->default(0);
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
        Schema::table('yz_member_relation', function (Blueprint $table) {
            //
        });
    }
}
