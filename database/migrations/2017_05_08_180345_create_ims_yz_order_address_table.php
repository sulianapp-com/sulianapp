<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzOrderAddressTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_order_address')) {
            Schema::table('yz_order_address', function (Blueprint $table) {

                if (!Schema::hasColumn('yz_order_address', 'province_id')) {

                    $table->integer('province_id')->default(0)->comment('省id');
                }

                if (!Schema::hasColumn('yz_order_address', 'city_id')) {

                    $table->integer('city_id')->default(0)->comment('市id');
                }
                if (!Schema::hasColumn('yz_order_address', 'district_id')) {

                    $table->integer('district_id')->default(0)->comment('区id');
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
        if (Schema::hasTable('yz_order_address')) {
            Schema::table('yz_order_address', function (Blueprint $table) {
                $table->dropColumn('province_id');
                $table->dropColumn('city_id');
                $table->dropColumn('district_id');
            });
        }
    }

}
