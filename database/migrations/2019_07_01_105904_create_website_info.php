<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebsiteInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       // 从0~65535全部是标准端口，但是从0~1024号端口是系统端口，用户无法修改
       //从1025~65534端口是系统为用户预留的端口，而65535号端口为系统保留
        if (!Schema::hasTable('yz_website_info')) {
            Schema::create('yz_website_info', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->nullable()->default(0);
                $table->string('website_url')->nullable()->default(0)->comment('站点');
                $table->string('founder_account',50)->nullable()->default(0)->comment('创始人账号');
                $table->string('founder_password',50)->nullable()->default(0)->comment('创始人密码');
                $table->string('server_ip',50)->nullable()->default(0)->comment('服务器IP');
                $table->string('root_password',50)->nullable()->default(0)->comment('服务器root密码');
                $table->string('ssh_port',50)->nullable()->default(0)->comment('ssh 端口');
                $table->string('database_address',50)->nullable()->default(0)->comment('数据库访问地址');
                $table->string('database_username',50)->nullable()->default(0)->comment('数据库用户名');
                $table->string('database_password',50)->nullable()->default(0)->comment('数据库密码');
                $table->string('root_directory',50)->nullable()->default(0)->comment('网站根目录');
                $table->string('qq',50)->nullable()->default(0)->comment('联系qq');
                $table->string('mobile',50)->nullable()->default(0)->comment('联系手机号');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
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
    }
}
