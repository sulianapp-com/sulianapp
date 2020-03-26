<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzMemberMonthOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_member_month_order')) {
            Schema::create('yz_member_month_order', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('member_id')->default(0);
                $table->smallInteger('year')->default(0);
                $table->smallInteger('month')->default(0);
                $table->integer('order_num')->default(0)->comment('订单数量');
                $table->decimal('order_price', 10)->default(0.00)->comment('订单总额');
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
    }
}
