<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsDefaultAndNoticeTypeToMessageTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_message_template')) {
            if (!Schema::hasColumn('yz_message_template', 'is_default')) {
                Schema::table('yz_message_template', function (Blueprint $table) {
                    $table->tinyInteger('is_default')->default(0);
                    $table->string('notice_type')->default(0);
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
