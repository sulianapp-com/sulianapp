<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTimeFieldsDefaultValueToYzOrderRefundTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Illuminate\Support\Facades\Schema::hasTable('yz_order_refund')) {
            \Illuminate\Support\Facades\Schema::table('yz_order_refund',
                function (Blueprint $table) {
                    $table->integer('create_time')->default(null)->nullable()->change();
                    $table->integer('refund_time')->default(null)->nullable()->change();
                    $table->integer('operate_time')->default(null)->nullable()->change();
                    $table->integer('send_time')->default(null)->nullable()->change();
                    $table->integer('return_time')->default(null)->nullable()->change();
                });
        }
        if (\Illuminate\Support\Facades\Schema::hasTable('yz_return_express')) {
            \Illuminate\Support\Facades\Schema::table('yz_return_express',
                function (Blueprint $table) {
                    $table->integer('created_at')->default(null)->nullable()->change();
                    $table->integer('updated_at')->default(null)->nullable()->change();
                });
        }
        if (\Illuminate\Support\Facades\Schema::hasTable('yz_resend_express')) {
            \Illuminate\Support\Facades\Schema::table('yz_resend_express',
                function (Blueprint $table) {
                    $table->integer('created_at')->default(null)->nullable()->change();
                    $table->integer('updated_at')->default(null)->nullable()->change();
                });
        }
        
        \Illuminate\Support\Facades\DB::update('update ' . app('db')->getTablePrefix() . 'yz_order_refund set create_time = null where create_time = 0');
        \Illuminate\Support\Facades\DB::update('update ' . app('db')->getTablePrefix() . 'yz_order_refund set refund_time = null where refund_time = 0');
        \Illuminate\Support\Facades\DB::update('update ' . app('db')->getTablePrefix() . 'yz_order_refund set operate_time = null where operate_time = 0');
        \Illuminate\Support\Facades\DB::update('update ' . app('db')->getTablePrefix() . 'yz_order_refund set send_time = null where send_time = 0');
        \Illuminate\Support\Facades\DB::update('update ' . app('db')->getTablePrefix() . 'yz_order_refund set return_time = null where return_time = 0');
        \Illuminate\Support\Facades\DB::update('update ' . app('db')->getTablePrefix() . 'yz_order_refund set end_time = null where return_time = 0');

        \Illuminate\Support\Facades\DB::update('update ' . app('db')->getTablePrefix() . 'yz_return_express set created_at = null where created_at = 0');
        \Illuminate\Support\Facades\DB::update('update ' . app('db')->getTablePrefix() . 'yz_return_express set updated_at = null where updated_at = 0');
        \Illuminate\Support\Facades\DB::update('update ' . app('db')->getTablePrefix() . 'yz_resend_express set created_at = null where created_at = 0');
        \Illuminate\Support\Facades\DB::update('update ' . app('db')->getTablePrefix() . 'yz_resend_express set updated_at = null where updated_at = 0');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
