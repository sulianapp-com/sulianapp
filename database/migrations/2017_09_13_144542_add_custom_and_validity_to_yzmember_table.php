<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomAndValidityToYzmemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Schema::hasTable('yz_member')) {
            Schema::table('yz_member', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_member', 'custom_value')) {
                    $table->string('custom_value', 255)->nullable();
                }
                if (!Schema::hasColumn('yz_member', 'validity')) {
                    $table->integer('validity')->nullable()->comment('等级有效期');
                }
            });
        }

        if (\Schema::hasTable('yz_member_level')) {
            Schema::table('yz_member_level', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_member_level', 'validity')) {
                    $table->integer('validity')->nullable()->comment('等级有效期');
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
