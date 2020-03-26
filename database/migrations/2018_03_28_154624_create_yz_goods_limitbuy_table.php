<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzGoodsLimitbuyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_goods_limitbuy')) {

            Schema::create('yz_goods_limitbuy', function (Blueprint $table) {
                $table->integer('id', true);//自增:设置第二个参数为true
                $table->integer('uniacid');
                $table->integer('goods_id');
                $table->tinyInteger('status')->default(0);
                $table->integer('start_time');
                $table->integer('end_time');

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
        Schema::dropIfExists('ims_yz_goods_limitbuy');
        //dropIfExists()判断表是否存在,如果存在则删除
    }
}
