<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEnabledToYzOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Schema::hasTable('yz_options')) {

            Schema::table('yz_options', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_options', 'enabled')) {
                    $table->boolean('enabled')->default(0);
                }
                if (!Schema::hasColumn('yz_options', 'uniacid')) {
                    $table->integer('uniacid')->default(0);
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
