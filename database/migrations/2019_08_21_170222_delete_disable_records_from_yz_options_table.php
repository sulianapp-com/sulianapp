<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class DeleteDisableRecordsFromYzOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_options')) {
            if (Schema::hasColumn('yz_options', 'enabled')){
                \Illuminate\Support\Facades\DB::table('yz_options')->where('enabled', 0)->delete();
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
