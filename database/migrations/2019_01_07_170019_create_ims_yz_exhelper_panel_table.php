<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzExhelperPanelTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_exhelper_panel')) {
            Schema::create('yz_exhelper_panel', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0);
                $table->string('panel_name')->nullable()->comment('电子面单名称');
                $table->string('panel_no')->nullable()->comment('电子面单客户账号');
                $table->string('panel_pass')->nullable()->comment('电子面单密码');
                $table->string('panel_sign')->nullable()->comment('月结编码');
                $table->string('panel_code')->nullable()->comment('收件网点标识');
                $table->string('panel_style')->nullable()->comment('模板样式');
                $table->string('exhelper_style')->nullable()->comment('快递类型');
                $table->boolean('isself')->default(0)->comment('是否通知快递员上门揽件');
                $table->boolean('isdefault')->default(0);
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
		Schema::drop('ims_yz_exhelper_panel');
	}

}
