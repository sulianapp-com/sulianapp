<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToYzOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_order')) {
            if (!Schema::hasColumn('yz_order', 'note')) {
                Schema::table('yz_order', function (Blueprint $table) {
                    $table->text('note')->nullable();
                });
            }
        }
        if (Schema::hasTable('yz_order')) {
            if (!Schema::hasColumn('yz_order', 'cost_amount')) {
                Schema::table('yz_order', function (Blueprint $table) {
                    $table->decimal('cost_amount', 14)->default(0.00);
                });
            }
        }
        if (Schema::hasTable('yz_order')) {
            if (!Schema::hasColumn('yz_order', 'shop_name')) {
                Schema::table('yz_order', function (Blueprint $table) {
                    $table->string('shop_name')->nullable();
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
