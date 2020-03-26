<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzAdminUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_admin_users')) {
            Schema::create('yz_admin_users', function (Blueprint $table) {
                $table->increments('uid')->comment('管理员用户表ID');
                $table->string('username', 100)->unique()->comment('用户名');
                $table->string('password')->comment('密码');
                $table->tinyInteger('status')->default(2)->comment('状态(0:超级管理员(admin); 1:审核; 2:有效; 3:禁用)');
                $table->tinyInteger('type')->default(1)->comment('类型(0:超级管理员(admin); 1:普通用户; 3:店员)');
                $table->text('remark')->comment('备注');
                $table->integer('application_number')->default(0)->comment('平台数量(0:代表不允许创建)');
                $table->integer('endtime')->default(0)->comment('到期时间(0:永久有效)');
                $table->integer('owner_uid');
                $table->string('salt', 10);
                $table->string('joinip', 15)->comment('加入ip');
                $table->integer('lastvisit')->comment('最后访问');
                $table->string('lastip', 15)->comment('最后访问ip');
                $table->rememberToken();
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
        Schema::dropIfExists('yz_admin_users');
    }
}
