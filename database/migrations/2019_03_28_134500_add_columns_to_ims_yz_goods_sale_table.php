<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToImsYzGoodsSaleTable extends Migration
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
                    if (!Schema::hasColumn('yz_goods_sale', 'point_type')) {
                        $table->boolean('point_type')->nullable()->default(0);
                        $table->decimal('max_once_point')->nullable()->default(0);
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
            $table->dropColumn('point_type');
            $table->dropColumn('max_once_point');
        });
    }
}