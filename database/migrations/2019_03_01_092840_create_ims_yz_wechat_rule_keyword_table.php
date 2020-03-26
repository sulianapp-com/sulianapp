<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzWechatRuleKeywordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_wechat_rule_keyword')) {
            Schema::create('yz_wechat_rule_keyword', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('rid')->default(0);
                $table->integer('uniacid')->default(0);
                $table->string('module')->default('')->nullable();
                $table->string('content')->default('')->nullable();
                $table->integer('type')->default(0)->nullable();
                $table->integer('displayorder')->default(0)->nullable();
                $table->integer('status')->default(0)->nullable();
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
        Schema::dropIfExists('yz_wechat_rule_keyword');
    }
}
