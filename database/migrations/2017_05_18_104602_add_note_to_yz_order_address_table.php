<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNoteToYzOrderAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_order_address')) {
            Schema::table('yz_order_address', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_order_address', 'note')) {
                    $table->text('note')->nullable();
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
        if (Schema::hasTable('yz_order_address')) {
            Schema::table('yz_order_address', function (Blueprint $table) {
                $table->dropColumn('note');
            });
        }
    }
}
