<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPluginIdToImsYzGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yz_goods', function (Blueprint $table) {
            if (!Schema::hasColumn('yz_goods', 'plugin_id')) {
                $table->integer('plugin_id')->default(0);
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
        Schema::table('yz_goods', function (Blueprint $table) {
            $table->dropColumn('plugin_id');
        });
    }
}
