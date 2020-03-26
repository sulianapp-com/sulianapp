<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImsYzInvitePage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_member_invitation_log')) {
            Schema::table('yz_member_invitation_log', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_member_invitation_log', 'mid')) {
                    $table->integer('mid')->nullable()->comment('会员id');
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
