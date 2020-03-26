<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSharePageDeailMemberRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_member_relation')) {
            Schema::table('yz_member_relation', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_member_relation', 'share_page_deail')) {
                    $table->tinyInteger('share_page_deail')->default(0)->comment('0:显示1:不显示');
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
