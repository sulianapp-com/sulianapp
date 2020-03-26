<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMemberFormToImsYzMember extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yz_member', function (Blueprint $table) {
            if (!Schema::hasColumn('yz_member', 'member_form')) {
                $table->text('member_form')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('yz_member', function (Blueprint $table) {
            $table->dropColumn('member_form');
        });
    }
}
