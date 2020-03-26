<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzWithdrawSetLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!\Schema::hasTable('yz_withdraw_set_log')) {
            Schema::create('yz_withdraw_set_log', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('withdraw_id')->nullable()->comment('ID');
                $table->boolean('poundage_type')->nullable()->default(0)->comment('提现手续费类型  0比例计算 ，1固定金额，');
                $table->decimal('poundage', 14)->nullable()->comment('手续费 poundage_tpye为0，则为比例，为1则为固定金额，');
                $table->decimal('poundage_full_cut', 14)->nullable()->comment('满额减免手续费');
                $table->decimal('withdraw_fetter', 14)->nullable()->comment('提现限制，提现最小额');
                $table->string('remark', 200)->nullable()->default('')->comment('备注');
                $table->integer('created_at')->nullable()->comment('创建时间');
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
		Schema::drop('yz_withdraw_set_log');
	}

}
