<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzOperationLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_operation_log')) {
            Schema::create('yz_operation_log', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0)->index('idx_uniacid');
                $table->integer('user_id')->default(0)->comment('操作人id');
                $table->string('user_name', 120)->default('')->comment('操作人');
                $table->string('modules', 100)->default('')->comment('模块');
                $table->string('type', 100)->default('')->comment('模块类别');
                $table->string('ip', 135)->default('')->comment('操作人IP');
                $table->string('old_content', 255)->default('')->comment('修改前内容');
                $table->string('new_content', 255)->default('')->comment('修改后内容');
                $table->string('field_name', 255)->default('')->comment('字段名称');
                $table->string('field', 255)->default('')->comment('修改的字段');
                $table->string('extend', 255)->default('');
                $table->string('mark', 255)->default('')->comment('修改所属的id');
                $table->text('input', 65535)->nullable();
                $table->tinyInteger('status')->default(0)->comment('0:记录成功|1：记录失败');
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
        Schema::dropIfExists('yz_operation_log');
    }
}
