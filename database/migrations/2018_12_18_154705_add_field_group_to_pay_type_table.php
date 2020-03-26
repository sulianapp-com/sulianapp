<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldGroupToPayTypeTable extends Migration
{
    /**
     * 对支付方式表增加分组列，默认为0
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_pay_type')) {
            Schema::table('yz_pay_type', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_pay_type', 'group_id')) {
                    $table->integer('group_id')->default(0);
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
