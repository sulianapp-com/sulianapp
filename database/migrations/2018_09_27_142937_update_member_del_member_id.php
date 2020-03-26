<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMemberDelMemberId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_member_del_log')) {
            Schema::table('yz_member_del_log', function (Blueprint $table) {
                if (Schema::hasColumn('yz_member_del_log', 'member)id')) {
                    $table->dropColumn('member)id');
                    $table->integer('member_id')->default(0)->index('del_uid');
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
