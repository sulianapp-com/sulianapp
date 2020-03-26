<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAllPointDeductToImsYzGoodsSaleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_goods_sale')) {
            Schema::table('yz_goods_sale',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_goods_sale', 'all_point_deduct')) {
                        $table->decimal('all_point_deduct', 10)->nullable();
                    }
                    if (!Schema::hasColumn('yz_goods_sale', 'has_all_point_deduct')) {
                        $table->integer('has_all_point_deduct')->nullable();
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
        Schema::table('yz_goods_sale', function (Blueprint $table) {
            $table->dropColumn('all_point_deduct');
            $table->integer('has_all_point_deduct');
        });
    }
}
