<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEnableTimeLimitToGoodsPrivilegeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_goods_privilege')) {
            if (!Schema::hasColumn('yz_goods_privilege', 'enable_time_limit')) {

                Schema::table('yz_goods_privilege', function (Blueprint $table) {
                    $table->tinyInteger('enable_time_limit')->nullable()->default(0);
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
        if (Schema::hasTable('yz_goods_privilege')) {
            if (Schema::hasColumn('yz_goods_privilege', 'enable_time_limit')) {

                Schema::table('yz_goods_privilege', function (Blueprint $table) {
                    $table->dropColumn('enable_time_limit');
                });
            }
        }
    }
}
