<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzOrderOperationTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_order_status')) {
            Schema::create('yz_order_status', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('name');
                $table->string('code');
                $table->integer('sort');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
            });
        }
        if (!Schema::hasTable('yz_member_order_operation')) {
            Schema::create('yz_member_order_operation', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('order_status_id');
                $table->string('name');
                $table->string('code');
                $table->string('value');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
            });
        }

        $sql = 'SELECT count(1) FROM ' . app('db')->getTablePrefix() . 'yz_order_status';
        if (!\Illuminate\Support\Facades\DB::select($sql)) {
            \Illuminate\Support\Facades\DB::insert('INSERT INTO `'.app('db')->getTablePrefix().'yz_order_status` (`id`, `name`, `code`, `sort`, `created_at`, `updated_at`, `deleted_at`)
VALUES
	(1, \'待付款\', \'waitPay\', 0, NULL, NULL, NULL),
	(2, \'待发货\', \'waitSend\', 0, NULL, NULL, NULL),
	(3, \'待收货\', \'waitReceive\', 0, NULL, NULL, NULL),
	(4, \'已完成\', \'complete\', 0, NULL, NULL, NULL),
	(5, \'已关闭\', \'close\', 0, NULL, NULL, NULL);
');
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        if (Schema::hasTable('yz_member_type')) {
//            Schema::dropIfExists('yz_member_type');
//        }

//        if (Schema::hasTable('yz_user_type')) {
//            Schema::dropIfExists('yz_user_type');
//        }
        if (Schema::hasTable('yz_order_status')) {
            Schema::dropIfExists('yz_order_status');
        }
//        if (Schema::hasTable('yz_order_user_operation')) {
//            Schema::dropIfExists('yz_order_user_operation');
//        }
        if (Schema::hasTable('yz_member_order_operation')) {
            Schema::dropIfExists('yz_member_order_operation');
        }
    }
}
