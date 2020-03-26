<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMessageTemplateNewsLink extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        //添加字段
        if (Schema::hasTable('yz_message_template')) {
            if (!Schema::hasColumn('yz_message_template', 'news_link')) {
                Schema::table('yz_message_template', function (Blueprint $table) {
                    $table->string('news_link')->nullable();
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
