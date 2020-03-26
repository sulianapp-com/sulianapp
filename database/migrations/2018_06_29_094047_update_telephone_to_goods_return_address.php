<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTelephoneToGoodsReturnAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_goods_return_address')) {
            Schema::table('yz_goods_return_address', function (Blueprint $table) {
                if (Schema::hasColumn('yz_goods_return_address', 'telephone')) {
                    $table->string('telephone', 50)->nullable()->change();
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
        if (Schema::hasTable('yz_goods_return_address')) {
            Schema::table('yz_goods_return_address', function (Blueprint $table) {
                $table->dropColumn('telephone');
            });
        }
    }
}
