<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzOrderPayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_order_pay')) {
            Schema::create('yz_order_pay', function (Blueprint $table) {
                $table->increments('id');
                $table->string('pay_sn', 23)->default('');
                $table->boolean('status')->default(0);
                $table->boolean('pay_type_id')->default(0);
                $table->integer('pay_time')->default(0);
                $table->integer('refund_time')->default(0);
                $table->string('order_ids', 500)->default('');
                $table->decimal('amount', 10)->default(0.00);
                $table->integer('uid');
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
        Schema::dropIfExists('yz_order_pay');
    }

}
