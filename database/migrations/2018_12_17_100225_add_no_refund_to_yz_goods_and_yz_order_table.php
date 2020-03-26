<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNoRefundToYzGoodsAndYzOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_goods')) {
            Schema::table('yz_goods', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_goods', 'no_refund')) {
                    $table->tinyInteger('no_refund')->default(0);
                }
            });
        }

        if (Schema::hasTable('yz_order')) {
            Schema::table('yz_order', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_order', 'no_refund')) {
                    $table->tinyInteger('no_refund')->default(0);
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
        if (Schema::hasTable('yz_goods')) {
            if (Schema::hasColumn('yz_goods', 'no_refund')) {
                Schema::table('yz_goods', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->dropColumn('no_refund');
                });
            }
        }

        if (Schema::hasTable('yz_order')) {
            if (Schema::hasColumn('yz_order', 'no_refund')) {
                Schema::table('yz_order', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->dropColumn('no_refund');
                });
            }
        }
    }
}
