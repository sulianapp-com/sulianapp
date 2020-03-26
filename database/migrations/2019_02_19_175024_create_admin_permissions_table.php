<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_admin_permissions')) {
            Schema::create('yz_admin_permissions', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->comment('权限名');
                $table->string('label')->comment('权限解释名称');
                $table->string('description')->comment('描述与备注');
                $table->tinyInteger('parent_id')->comment('级别');
                $table->string('icon')->comment('图标');
                $table->timestamps();
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
        Schema::dropIfExists('yz_admin_permissions');
    }
}
