<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzPayWithdrawOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_pay_withdraw_order')) {
            Schema::create('yz_pay_withdraw_order', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid');
                $table->integer('member_id');
                $table->string('int_order_no', 32);
                $table->string('out_order_no', 32);
                $table->string('trade_no', 255)->nullable();
                $table->decimal('price', 14, 2);
                $table->string('type', 255);
                $table->integer('status');
                $table->integer('created_at')->default(0);
                $table->integer('updated_at')->default(0);
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
        Schema::dropIfExists('yz_pay_withdraw_order');
    }
}
