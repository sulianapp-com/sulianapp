<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateGoodsDispatchDispatcPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //修改运费属性
        if (Schema::hasTable('yz_goods_dispatch')) {
            if (Schema::hasColumn('yz_goods_dispatch', 'dispatch_price')) {
                Schema::table('yz_goods_dispatch', function (Blueprint $table) {
                    $table->decimal('dispatch_price',14)->nullable()->change();
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
