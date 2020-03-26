<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzAdvertisementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_advertisement')) {
            Schema::create('yz_advertisement', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0)->index('idx_uniacid');
                $table->string('name', 200)->default('')->nullable()->comment('标题');
                $table->string('thumb', 255)->default('')->nullable()->comment('图片');
                $table->string('adv_url')->default('')->nullable()->comment('广告链接');
                $table->integer('sort_by')->default(0)->nullable()->comment('排序');
                $table->tinyInteger('status')->default(0)->comment('0:不显示|1：显示');
                $table->string('extend', 255)->default('');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
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
        Schema::dropIfExists('yz_advertisement');
    }
}
