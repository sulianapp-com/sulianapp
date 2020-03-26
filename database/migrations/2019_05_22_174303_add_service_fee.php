<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddServiceFee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //添加字段
        if (Schema::hasTable('yz_goods_service')) {
            if (!Schema::hasColumn('yz_goods_service', 'serviceFee')) {
                Schema::table('yz_goods_service', function (Blueprint $table) {
                    $table->decimal('serviceFee',14)->nullable();
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
