<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderPending extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_order_pending')) {
            Schema::create('yz_order_pending', function(Blueprint $table) {
                $table->integer('id', true);
                $table->integer('order_id')->index('idx_order_id');
                $table->integer('model_id')->index('idx_model_id');
                $table->string('model_type');
                $table->string('note');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
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
        if (Schema::hasTable('yz_order_pending')) {
            Schema::dropIfExists('yz_order_pending');

        }
    }
}
