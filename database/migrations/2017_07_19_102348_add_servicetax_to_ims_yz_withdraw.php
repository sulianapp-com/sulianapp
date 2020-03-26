<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddServicetaxToImsYzWithdraw extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Schema::hasTable('yz_withdraw')) {
            Schema::table('yz_withdraw', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_withdraw', 'servicetax')) {
                    $table->decimal('servicetax', 12)->nullable()->comment('劳务税');
                }
                if (!Schema::hasColumn('yz_withdraw', 'servicetax_rate')) {
                    $table->integer('servicetax_rate')->nullable()->comment('劳务税比例');
                }
                if (!Schema::hasColumn('yz_withdraw', 'actual_servicetax')) {
                    $table->decimal('actual_servicetax', 12)->nullable()->comment('最终劳务税');
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
        if (\Schema::hasTable('yz_withdraw')) {
            Schema::table('yz_withdraw', function (Blueprint $table) {
                $table->dropColumn('servicetax');
                $table->dropColumn('servicetax_rate');
                $table->dropColumn('actual_servicetax');
            });
        }
    }
}
