<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzUsersProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_users_profile')) {
            Schema::create('yz_users_profile', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uid')->comment('用户id');
                $table->string('realname', 100)->comment('姓名');
                $table->string('avatar')->comment('头像');
                $table->string('mobile', 11)->comment('手机号');
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
        Schema::dropIfExists('yz_users_profile');
    }
}
