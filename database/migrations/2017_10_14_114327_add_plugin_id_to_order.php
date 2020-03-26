<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPluginIdToOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_order')) {
            Schema::table('yz_order',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_order', 'plugin_id')) {
                        $table->integer('plugin_id')->default(0);
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
        Schema::table('yz_order', function (Blueprint $table) {
            $table->dropColumn('plugin_id');
        });
    }
}
