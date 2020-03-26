<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzGoodsReturnAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_goods_return_address')) {
            Schema::create('yz_goods_return_address',function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid');
                $table->string('address_name' ,32)->nullable();
                $table->string('contact', 20);
                $table->string('mobile', 11);
                $table->string('telephone', 11);
                $table->integer('province_id');
                $table->string('province_name', 32);
                $table->integer('city_id');
                $table->string('city_name', 32);
                $table->integer('district_id');
                $table->string('district_name', 32);
                $table->integer('street_id')->nullable();
                $table->string('street_name', 32)->nullable();
                $table->string('address', 512);
                $table->integer('plugins_id')->default(0);
                $table->integer('store_id')->default(0);
                $table->integer('supplier_id')->default(0);
                $table->boolean('is_default')->default(0);
                $table->integer('created_at')->default(0);
                $table->integer('updated_at')->default(0);
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
        if (Schema::hasTable('ims_yz_goods_return_address')) {

            Schema::drop('ims_yz_goods_return_address');
        }
    }
}
