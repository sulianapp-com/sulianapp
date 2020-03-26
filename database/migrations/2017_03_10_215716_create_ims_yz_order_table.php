<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzOrderTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_order')) {
            Schema::create('yz_order', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->unsigned()->default(0);
                $table->integer('member_id')->default(0);
                $table->string('order_sn', 23)->default('');
                $table->integer('price')->default(0);
                $table->integer('goods_price')->default(0);
                $table->boolean('status')->default(0);
                $table->integer('create_time')->default(0);
                $table->boolean('is_deleted')->default(0);
                $table->boolean('is_member_deleted')->default(0);
                $table->text('change_price_detail', 65535)->nullable();
                $table->integer('finish_time')->default(0);
                $table->integer('pay_time')->default(0);
                $table->integer('send_time')->default(0);
                $table->integer('cancel_time')->default(0);
                $table->integer('created_at')->default(0);
                $table->integer('updated_at')->default(0);
                $table->integer('deleted_at')->default(0);
                $table->integer('cancel_pay_time')->default(0);
                $table->integer('cancel_send_time')->default(0);
                $table->integer('dispatch_type_id')->default(0);
                $table->integer('pay_type_id')->default(0);
                $table->integer('is_plugin')->unsigned()->default(0);
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
        Schema::dropIfExists('yz_order');
    }

}
