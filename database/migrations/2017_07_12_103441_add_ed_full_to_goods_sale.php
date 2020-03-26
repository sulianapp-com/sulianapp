<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEdFullToGoodsSale extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Schema::hasTable('yz_goods_sale')) {

            Schema::table('yz_goods_sale', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_goods_sale', 'ed_full')) {
                    $table->decimal('ed_full', 10)->nullable()->default(0.00);
                }
                if (!Schema::hasColumn('yz_goods_sale', 'ed_reduction')) {
                    $table->decimal('ed_reduction', 10)->nullable()->default(0.00);
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
        if (\Schema::hasTable('yz_goods_sale')) {
            Schema::table('yz_goods_sale', function (Blueprint $table) {
                $table->dropColumn('ed_full');
                $table->dropColumn('ed_reduction');
            });
        }
    }
}
