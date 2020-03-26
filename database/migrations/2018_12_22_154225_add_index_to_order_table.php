<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (Schema::hasTable('yz_order')) {
            try{
                Schema::table('yz_order', function (Blueprint $table) {
                    $table->index('uniacid');
                    $table->index('uid');
                    $table->index('order_sn');
                    $table->index('plugin_id');
                    $table->index('status');
                });
            }catch (\Exception $e){
                \Log::error($e);
            }

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
