<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzOrderGoodsCoinExchangeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_order_goods_coin_exchange')) {
            Schema::create('yz_order_goods_coin_exchange',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('order_goods_id')->nullable();
                    $table->integer('uid')->nullable();
                    $table->string('code')->nullable();
                    $table->decimal('amount',14,2)->nullable();
                    $table->decimal('coin',14,2)->nullable();
                    $table->integer('created_at')->nullable();
                    $table->integer('updated_at')->nullable();
                    $table->integer('deleted_at')->nullable();
                    $table->index(['order_goods_id']);
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
        Schema::drop('yz_order_goods_coin_exchange');
    }
}
