<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UodateYzOrderConpanyNumberChang extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //修改单位识别号字段属性
        if (Schema::hasTable('yz_order')) {
            if (Schema::hasColumn('yz_order', 'company_number')) {
                Schema::table('yz_order', function (Blueprint $table) {
                    $table->string('company_number',255)->nullable()->change();
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
