<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MinAppTemplateMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_mini_app_template_message')) {
            Schema::create('yz_mini_app_template_message', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid');
                $table->string('title');
                $table->string('template_id', 45);
                $table->text('data', 65535)->nullable();
                $table->integer('is_default')->nullable();
                $table->integer('is_open')->default(0);
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
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
        Schema::dropIfExists('yz_mini_app_template_message');
    }
}
