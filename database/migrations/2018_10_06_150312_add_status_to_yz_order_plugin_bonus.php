<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToYzOrderPluginBonus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_order_plugin_bonus')) {
            Schema::table('yz_order_plugin_bonus', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_order_plugin_bonus', 'content')) {
                    $table->string('content')->nullable();
                }
                if (!Schema::hasColumn('yz_order_plugin_bonus', 'status')) {
                    $table->tinyInteger('status')->default(0);
                }
                if (!Schema::hasColumn('yz_order_plugin_bonus', 'undividend')) {
                    $table->decimal('undividend',14)->default(0.00);
                }
                if (!Schema::hasColumn('yz_order_plugin_bonus', 'uniacid')) {
                    $table->integer('uniacid')->default(0);
                }
                if (!Schema::hasColumn('yz_order_plugin_bonus', 'uniacid')) {
                    $table->string('order_sn')->nullable();
                }
                if (!Schema::hasColumn('yz_order_plugin_bonus', 'uniacid')) {
                    $table->decimal('price',14)->default(0.00);
                }
                if (!Schema::hasColumn('yz_order_plugin_bonus', 'uniacid')) {
                    $table->integer('member_id')->default(0);
                }
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
        //
    }
}
