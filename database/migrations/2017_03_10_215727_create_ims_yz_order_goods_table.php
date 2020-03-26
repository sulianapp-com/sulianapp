<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzOrderGoodsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_order_goods')) {
            Schema::create('yz_order_goods', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0);
                $table->integer('order_id')->default(0);
                $table->integer('goods_id')->default(0);
                $table->integer('total')->default(1);
                $table->integer('create_at')->default(0);
                $table->integer('price')->default(0);
                $table->string('goods_sn', 50)->default('');
                $table->integer('member_id')->default(0);
                $table->string('thumb', 50);
                $table->string('title', 50);
                $table->integer('goods_price')->default(0);
                $table->integer('goods_option_id');
                $table->integer('goods_option_title');
                $table->integer('product_sn');
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
        Schema::dropIfExists('yz_order_goods');
    }

}
