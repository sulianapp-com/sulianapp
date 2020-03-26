<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzOrderPluginBonusTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_order_plugin_bonus')) {

            Schema::create('yz_order_plugin_bonus', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('order_id')->default(0)->index('idx_order_id');
                $table->string('table_name', 100)->default('');
                $table->string('ids', 1000)->default('');
                $table->string('code', 100)->default('')->index('idx_code');
                $table->decimal('amount', 11)->default(0.00);
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
        if (Schema::hasTable('yz_order_plugin_bonus')) {

            Schema::drop('ims_yz_order_plugin_bonus');
        }
    }

}
