<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSelfmoneyToYzMemberRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_member_relation')) {
            if (!Schema::hasColumn('yz_member_relation', 'become_selfmoney')) {
                Schema::table('yz_member_relation', function (Blueprint $table) {
                    $table->decimal('become_selfmoney', 15, 2)->default('0.00');
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
