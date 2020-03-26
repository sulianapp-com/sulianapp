<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEventsToYzOrderJob extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_order_created_job')) {
            if (!Schema::hasColumn('yz_order_created_job', 'events')) {
                Schema::table('yz_order_created_job', function (Blueprint $table) {
                    $table->text('events')->nullable();
                });
            }
        }
        if (Schema::hasTable('yz_order_paid_job')) {
            if (!Schema::hasColumn('yz_order_paid_job', 'events')) {
                Schema::table('yz_order_paid_job', function (Blueprint $table) {
                    $table->text('events')->nullable();
                });
            }
        }
        if (Schema::hasTable('yz_order_received_job')) {
            if (!Schema::hasColumn('yz_order_received_job', 'events')) {
                Schema::table('yz_order_received_job', function (Blueprint $table) {
                    $table->text('events')->nullable();
                });
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
