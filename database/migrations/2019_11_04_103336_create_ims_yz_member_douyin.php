<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzMemberDouyin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (!Schema::hasTable('yz_member_douyin')) {
            Schema::create('yz_member_douyin', function (Blueprint $table) {
                $table->integer('douyin_id', true);
                $table->integer('uniacid');
                $table->integer('member_id');
                $table->string('openid', 50);
                $table->string('nickname', 20);
                $table->string('avatar');
                $table->boolean('gender');
                $table->integer('created_at')->unsigned()->default(0);
                $table->integer('updated_at')->unsigned()->default(0);
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
        //
        Schema::dropIfExists('yz_member_douyin');
    }
}
