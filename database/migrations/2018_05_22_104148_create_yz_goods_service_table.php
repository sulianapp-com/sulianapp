<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzGoodsServiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!schema::hasTable('yz_goods_service')) {
            Schema::create('yz_goods_service', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid');
                $table->integer('goods_id')->nullable()->default(0)->index('idx_goods');
                $table->tinyInteger('is_automatic')->comment('自动上下架 1：是')->default(0);
                $table->integer('on_shelf_time')->nullable();
                $table->integer('lower_shelf_time')->nullable();
                $table->tinyInteger('is_refund')->nullable()->default(1)->comment('是否支持退货 1：是');
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
        Schema::dropIfExists('yz_goods_service');
        
    }
}
