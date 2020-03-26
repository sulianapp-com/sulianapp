<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {    //创建发票表
        if (!Schema::hasTable('yz_order_invoice')) {
            Schema::create('yz_order_invoice', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uid');
                $table->integer('uniacid');
                $table->integer('order_id');
                $table->integer('invoice_type');
                $table->integer('rise_type');
                $table->string('call');
                $table->string('company_number')->default(0);
                $table->string('invoice')->default(0);
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
        Schema::dropIfExists('order_invoice');
    }
}
