<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzFirstOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_first_order')) {
            Schema::create('yz_first_order',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('order_id')
                        ->nullable();
                    $table->integer('uid')
                        ->nullable();
                    $table->text('shop_order_set', 65535)
                        ->nullable();
                    $table->integer('created_at')
                        ->nullable();
                    $table->integer('updated_at')
                        ->nullable();
                    $table->integer('deleted_at')
                        ->nullable();
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
        Schema::drop('yz_first_order');
    }
}
