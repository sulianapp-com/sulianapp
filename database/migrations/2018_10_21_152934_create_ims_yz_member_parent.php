<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzMemberParent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_member_parent')) {
            Schema::create('yz_member_parent', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0);
                $table->integer('parent_id')->default(0)->comment('父id');
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
        Schema::drop('yz_member_parent');
    }
}
