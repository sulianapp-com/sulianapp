<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsVirtualToYzMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Schema::hasTable('yz_order')) {
            Schema::table('yz_order', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_order', 'is_virtual')) {
                    $table->tinyInteger('is_virtual')->default(0);
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
        if (\Schema::hasTable('yz_order')) {
            Schema::table('yz_order', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_order', 'is_virtual')) {
                    $table->dropColumn('is_virtual');
                }
            });
        }
    }
}
