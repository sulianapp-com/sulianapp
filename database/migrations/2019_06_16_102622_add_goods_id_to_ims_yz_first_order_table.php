<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGoodsIdToImsYzFirstOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_first_order')) {
            Schema::table('yz_first_order',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_first_order', 'goods_id')) {
                        $table->integer('goods_id')->nullable();
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
        Schema::table('yz_first_order', function (Blueprint $table) {
            $table->integer('goods_id');
        });
    }
}
