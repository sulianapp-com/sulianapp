<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzQrcodeStatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_qrcode_stat')) {
            Schema::create('yz_qrcode_stat', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0)->nullable();
                $table->integer('acid')->default(0)->nullable();
                $table->integer('qid')->default(0)->nullable();
                $table->string('openid')->default('')->nullable();
                $table->integer('type')->default(0)->nullable();
                $table->integer('qrcid')->default(0)->nullable();
                $table->string('scene_str')->default('')->nullable();
                $table->string('name')->default('')->nullable();
                $table->integer('createtime')->default(0)->nullable();
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
        if (Schema::hasTable('yz_qrcode_stat')) {
            Schema::dropIfExists('yz_qrcode_stat');
        }
    }
}
