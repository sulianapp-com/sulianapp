<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPoundageToImsYzPointTransfer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_point_transfer')) {
            Schema::table('yz_point_transfer',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_point_transfer', 'poundage')) {
                        $table->integer('poundage')->nullable()->default(0);
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
        Schema::dropIfExists('yz_point_transfer');
    }
}
