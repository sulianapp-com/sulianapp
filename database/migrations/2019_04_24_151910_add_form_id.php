<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFormId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        //添加字段
        if (Schema::hasTable('yz_member_mini_app')) {
            if (!Schema::hasColumn('yz_member_mini_app', 'formId')) {
                Schema::table('yz_member_mini_app', function (Blueprint $table) {
                    $table->string('formId')->nullable();
                    $table->string('formId_create_time')->nullable();
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
        if (Schema::hasTable('yz_member_mini_app')) {
            if (Schema::hasColumn('yz_member_mini_app', 'formId')) {
                Schema::table('yz_member_mini_app', function (Blueprint $table) {
                    $table->dropColumn('formId_create_time');
                    $table->dropColumn('formId');
                });
            }
        }
        //
    }
}
