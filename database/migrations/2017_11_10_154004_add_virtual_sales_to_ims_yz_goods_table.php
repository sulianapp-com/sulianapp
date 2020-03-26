<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVirtualSalesToImsYzGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_goods')) {
            Schema::table('yz_goods',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_goods',
                        'virtual_sales')
                    ) {
                        $table->integer('virtual_sales')->nullable()->default(0);
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
        Schema::table('yz_goods', function (Blueprint $table) {
            $table->dropColumn('virtual_sales');
        });
    }
}
