<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMinPointDeductToGoodsSaleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_goods_sale')) {
            if (!Schema::hasColumn('yz_goods_sale', 'min_point_deduct')) {
                Schema::table('yz_goods_sale', function (Blueprint $table) {
                    $table->string('min_point_deduct', 255)->nullable();
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
        if (Schema::hasTable('yz_goods_sale')) {
            if (Schema::hasColumn('yz_goods_sale', 'min_point_deduct')) {
                Schema::table('yz_goods_sale', function (Blueprint $table) {
                    $table->dropColumn('min_point_deduct');
                });
            }
        }
    }
}
