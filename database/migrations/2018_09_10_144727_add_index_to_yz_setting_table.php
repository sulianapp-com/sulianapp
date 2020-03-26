<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToYzSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_setting')) {
            Schema::table('yz_setting', function (Blueprint $table) {
                $idx = \Illuminate\Support\Facades\DB::select('show index from ' . app('db')->getTablePrefix() . 'yz_setting where key_name="idx_group_uniacid"');

                if (!$idx) {
                    \Illuminate\Support\Facades\DB::statement('alter table ' . app('db')->getTablePrefix() . 'yz_setting add index `idx_group_uniacid`(`group`, `uniacid`)');
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
