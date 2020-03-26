<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzExcelRechargeRecordsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_excel_recharge_records')) {
            Schema::create('yz_excel_recharge_records', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid');
                $table->integer('total')->default(0)->comment('总个数');
                $table->decimal('amount', 14)->default(0.00)->comment('总数量');
                $table->integer('failure')->default(0)->comment('失败数');
                $table->decimal('success', 14)->default(0.00)->comment('成功数');
                $table->string('source', 45)->nullable();
                $table->string('remark', 100)->nullable()->comment('备注信息');
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
		//Schema::drop('ims_yz_excel_recharge_records');
	}

}
