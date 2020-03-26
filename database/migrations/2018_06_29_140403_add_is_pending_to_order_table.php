<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class AddIsPendingToOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_order')) {
            if (!Schema::hasColumn('yz_order', 'is_pending')) {
                Schema::table('yz_order', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->tinyInteger('is_pending')->default(0);
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
        if (Schema::hasTable('yz_order')) {
            if (Schema::hasColumn('yz_order', 'is_pending')) {
                Schema::table('yz_order', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->dropColumn('is_pending');
                });
            }
        }
    }
}
