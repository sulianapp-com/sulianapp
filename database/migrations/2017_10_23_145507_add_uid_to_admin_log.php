<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUidToAdminLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_admin_operation_log')) {
            Schema::table('yz_admin_operation_log',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_admin_operation_log', 'uid')) {
                        $table->integer('uid')->nullable();
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
        if (Schema::hasTable('yz_admin_operation_log')) {
            if (Schema::hasColumn('yz_admin_operation_log', 'uid')) {
                Schema::table('yz_admin_operation_log', function (Blueprint $table) {
                    $table->dropColumn('uid');
                });
            }

        }
    }
}
