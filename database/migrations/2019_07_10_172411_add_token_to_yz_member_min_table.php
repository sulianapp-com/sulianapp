<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTokenToYzMemberMinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_member_mini_app')) {
            Schema::table('yz_member_mini_app', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_member_mini_app', 'access_token')) {
                    $table->string('access_token', 1000)->default(0);
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
