<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOwnerIdToYzMemberHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_member_history')) {
            Schema::table('yz_member_history', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_member_history', 'owner_id')) {
                    $table->integer('owner_id')->default(0);
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
        if (Schema::hasTable('yz_member_history')) {
            Schema::table('yz_member_history', function (Blueprint $table) {
                $table->dropColumn('owner_id');
            });
        }
    }
}
