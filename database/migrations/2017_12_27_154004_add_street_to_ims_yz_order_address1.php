<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStreetToImsYzOrderAddress1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_order_address')) {
            Schema::table('yz_order_address',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_order_address',
                        'street_id')
                    ) {
                        $table->integer('street_id')->nullable()->default(0);
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
        Schema::table('yz_order_address', function (Blueprint $table) {
            $table->dropColumn('street_id');
        });
    }
}
