<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPointLoveSet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_point_love_set')) {
            if (!Schema::hasColumn('yz_point_love_set', 'transfer_love')) {
                Schema::table('yz_point_love_set', function (Blueprint $table) {
                    $table->string('transfer_love')->default('');
                    $table->string('transfer_integral')->default('');
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
