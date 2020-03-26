<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsPushToYzGoodsSaleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (\Schema::hasTable('yz_goods_sale')) {

            if (!Schema::hasColumn('yz_goods_sale', 'is_push')) {
                Schema::table('yz_goods_sale', function ($table) {
                    $table->tinyInteger('is_push')->default(0);
                });
            }

            if (!Schema::hasColumn('yz_goods_sale', 'push_goods_ids')) {
                Schema::table('yz_goods_sale', function ($table) {
                    $table->string('push_goods_ids','1000')->nullable();
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
