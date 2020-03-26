<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNeedAddressToYzOrderAndYzGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        //todo 兼容商品是虚拟类型的，下单页不填写地址也能下单

        if (Schema::hasTable('yz_goods')) {
            Schema::table('yz_goods', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_goods', 'need_address')) {
                    $table->tinyInteger('need_address')->default(0)->comment('是否需要填写收货地址 0:是1:否');
                }
            });
        }

        if (Schema::hasTable('yz_order')) {
            Schema::table('yz_order', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_order', 'need_address')) {
                    $table->tinyInteger('need_address')->default(0)->comment('是否需要填写收货地址 0:是1:否');
                }
            });
        }

        if (Schema::hasTable('yz_order_goods')) {
            Schema::table('yz_order_goods', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_order_goods', 'need_address')) {
                    $table->tinyInteger('need_address')->default(0)->comment('是否需要填写收货地址 0:是1:否');
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
        //
    }
}
