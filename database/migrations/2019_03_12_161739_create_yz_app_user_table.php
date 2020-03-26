<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzAppUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_app_user')) {
            Schema::create('yz_app_user', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->comment('平台id');
                $table->integer('uid')->comment('用户id');
                $table->string('role')->comment('用户角色(manager:管理员; operator: 操作员; clerk:操作员)');
                $table->integer('rank')->default(0)->comment('公众号及小程序置顶排序');
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
        Schema::dropIfExists('yz_app_user');
    }
}
