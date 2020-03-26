<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzMemberLowerOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (!Schema::hasTable('yz_member_lower_order')) {
            Schema::create('yz_member_lower_order', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uid')->default(0);
                $table->integer('uniacid')->default(0);
                $table->integer('first_order_quantity')->default(0)->comment('一级下线订单数');
                $table->integer('first_order_amount')->default(0)->comment('一级下线订单金额');
                $table->integer('second_order_quantity')->default(0)->comment('二级下线订单数');
                $table->integer('second_order_amount')->default(0)->comment('二级下线订单金额');
                $table->integer('third_order_quantity')->default(0)->comment('三级下线订单数');
                $table->integer('third_order_amount')->default(0)->comment('三级下线订单金额');
                $table->integer('team_order_quantity')->default(0)->comment('团队下线订单数');
                $table->integer('team_order_amount')->default(0)->comment('团队下线订单金额');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
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
        Schema::drop('yz_member_lower_order');
    }
}
