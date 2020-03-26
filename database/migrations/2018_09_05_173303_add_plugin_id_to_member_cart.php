<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPluginIdToMemberCart extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_member_cart')) {
            Schema::table('yz_member_cart', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_member_cart', 'plugin_id')) {
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
        if (Schema::hasTable('yz_member_cart')) {
            if (Schema::hasColumn('yz_member_cart', 'plugin_id')) {
                Schema::table('yz_member_cart', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->dropColumn('plugin_id');
                });
            }
        }
    }
}
