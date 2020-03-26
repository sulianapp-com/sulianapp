<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzGoodsFilteringTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_goods_filtering')) {
            Schema::create('yz_goods_filtering', function(Blueprint $table) {
                $table->integer('id', true);
                $table->integer('goods_id')->nullable()->default(0)->index('idx_goods');
                $table->integer('filtering_id')->nullable()->default(0)->comment('过滤id');
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
