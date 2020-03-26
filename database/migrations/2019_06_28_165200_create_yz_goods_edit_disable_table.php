<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzGoodsEditDisableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_goods_edit_disable')) {
            Schema::create('yz_goods_edit_disable', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->comment('公众号');
                $table->integer('goods_id')->comment('商品id');
                $table->string('message')->comment('提示信息，简述禁止编辑商品的原因,如:商品已参加XX活动，不可编辑，请等待XX活动结束!');
                $table->string('edit_key')->comment('关键字,通过该字段可区分一条记录!');

                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
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
        if (Schema::hasTable('yz_goods_edit_disable')) {
            Schema::drop('yz_goods_edit_disable');
        }
    }
}
