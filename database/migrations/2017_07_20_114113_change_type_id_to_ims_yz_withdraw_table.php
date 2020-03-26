<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTypeIdToImsYzWithdrawTable extends Migration
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
                if (Schema::hasColumn('yz_withdraw', 'type_id')) {
                    $table->text('type_id')->change();
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
        Schema::table('yz_withdraw', function (Blueprint $table) {
            if (Schema::hasColumn('yz_withdraw', 'type_id')) {
                $table->string('type_id')->change();
            }
        });
    }
}