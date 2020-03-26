<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzMemberChildren extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_member_children')) {
            Schema::create('yz_member_children', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0);
                $table->integer('child_id')->default(0)->comment('下级id');
                $table->integer('level')->default(0)->comment('层级');
                $table->integer('member_id')->default(0)->comment('会员ID');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->index(['member_id', 'level']);
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
        Schema::drop('yz_member_children');
    }
}
