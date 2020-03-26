<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzDistributionOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_distribution_order')) {
            Schema::create('yz_distribution_order', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uid')->default(0);
                $table->integer('uniacid')->default(0);
                $table->integer('commission_order_num')->default(0)->comment('分销订单数');
                $table->decimal('commission_order_prices', 65)->default(0)->comment('分销订单业绩');
                $table->integer('team_people_num')->default(0)->comment('团队总人数');
                $table->decimal('team_commission_order_prices', 65)->default(0)->comment('团队分销订单业绩');
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
        Schema::drop('yz_distribution_order');
    }
}
