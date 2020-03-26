<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzOrderRequestTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!\Schema::hasTable('yz_order_request')) {

            Schema::create('yz_order_request', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('order_id');
                $table->text('request')->nullable()->comment('请求参数');
                $table->integer('updated_at')->nullable();
                $table->integer('created_at')->nullable();
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
        if (\Schema::hasTable('yz_order_request')) {
            Schema::drop('yz_order_request');
        }
    }

}
