<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPoundageTypeToYzWithdraw extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_withdraw')) {
            Schema::table('yz_withdraw',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_withdraw','poundage_type')){
                        $table->tinyInteger('poundage_type')->nullable()->default(0)->after('poundage_rate')->comment('手续费类型 0:比例 1:固定');
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
        //
    }
}
