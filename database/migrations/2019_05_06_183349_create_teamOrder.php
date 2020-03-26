<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_team_order_statistics')) {
            Schema::create('yz_team_order_statistics', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uid')->default(0);
                $table->integer('uniacid')->default(0);
                $table->integer('team_order_quantity')->default(0)->comment('团队下线订单数');
                $table->integer('team_order_amount')->default(0)->comment('团队下线订单金额');
                $table->integer('pay_count')->default(0)->nullable()->comment('团队支付人数');
                $table->integer('team_count')->default(0)->nullable()->comment('团队总人数');
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
