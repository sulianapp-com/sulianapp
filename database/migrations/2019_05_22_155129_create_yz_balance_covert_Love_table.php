<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzBalanceCovertLoveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //余额转换爱心值log表
        if (!Schema::hasTable('yz_balance_covert_love')) {
            Schema::create('yz_balance_covert_love', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid');
                $table->integer('member_id')->comment('会员ID');
                $table->integer('covert_amount')->comment('转化金额');
                $table->integer('status')->comment('转化状态');
                $table->string('order_sn', 23)->default('')->comment('订单号');
                $table->string('remark', 30)->default('')->comment('备注');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
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
