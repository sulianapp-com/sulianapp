<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzQrcodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_qrcode')) {
            Schema::create('yz_qrcode', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0)->nullable();
                $table->integer('acid')->default(0)->nullable();
                $table->string('type')->default('')->nullable();
                $table->integer('extra')->default(0)->nullable();
                $table->integer('qrcid')->default(0)->nullable();
                $table->string('scene_str')->default('')->nullable();
                $table->string('name')->default('')->nullable();
                $table->string('keyword')->default('')->nullable();
                $table->integer('model')->default(0)->nullable();
                $table->string('ticket')->default('')->nullable();
                $table->string('url')->default('')->nullable();
                $table->integer('expire')->default(0)->nullable();
                $table->integer('subnum')->default(0)->nullable();
                $table->integer('createtime')->default(0)->nullable();
                $table->integer('status')->default(0)->nullable();
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
        if (Schema::hasTable('yz_qrcode')) {
            Schema::dropIfExists('yz_qrcode');
        }
    }
}
