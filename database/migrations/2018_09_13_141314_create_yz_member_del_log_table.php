<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzMemberDelLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         if (!Schema::hasTable('yz_member_del_log')) {
            Schema::create('yz_member_del_log', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid');
                $table->integer('member_id')->default(0)->index('del_uid');
                $table->tinyInteger('type')->default(0);
                $table->text('value', 65535);
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
        Schema::dropIfExists('yz_member_del_log');
    }
}
