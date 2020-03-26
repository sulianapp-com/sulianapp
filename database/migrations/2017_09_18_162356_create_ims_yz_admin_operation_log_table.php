<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzAdminOperationLogTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!\Schema::hasTable('yz_admin_operation_log')) {

            Schema::create('yz_admin_operation_log', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('admin_uid')->nullable()->comment('管理员');
                $table->string('table_name', 50)->nullable()->comment('表名');
                $table->string('table_id', 50)->nullable()->comment('表名');
                $table->text('before', 65535)->nullable()->comment('改变前');
                $table->text('after', 65535)->nullable()->comment('改变后');
                $table->string('ip', 20)->nullable()->default('')->comment('操作者ip');
                $table->string('before_identify', 32)->nullable()->default('')->comment('修改前标识');
                $table->string('after_identify', 32)->nullable()->comment('修改后标识');
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
        if (\Schema::hasTable('yz_admin_operation_log')) {
            Schema::drop('yz_admin_operation_log');
        }
    }

}
