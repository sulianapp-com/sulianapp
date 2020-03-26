<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertImsYzSettingSupervisordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_setting')) {
            \Illuminate\Support\Facades\DB::table('yz_setting')
                ->insert([
                    'uniacid' => 0,
                    'group' => 'shop',
                    'key' => 'supervisor',
                    'type' => 'string',
                    'value' => 'http://127.0.0.1',
                ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yz_setting');
    }
}
