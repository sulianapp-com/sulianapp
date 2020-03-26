<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeEnableTimeLimitToGoodsPrivilegeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Schema::hasTable('yz_goods_privilege')) {
            Schema::table('yz_goods_privilege', function (Blueprint $table) {
                if (Schema::hasColumn('yz_goods_privilege', 'enable_time_limit')) {
                    $table->dropColumn('enable_time_limit');
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

    }
}
