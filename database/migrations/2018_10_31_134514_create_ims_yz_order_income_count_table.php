<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateImsYzOrderIncomeCountTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_order_income_count')) {
            Schema::create('yz_order_income_count', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable();
                $table->integer('uid')->comment('购买者id');
                $table->string('buy_name')->nullable()->comment('购买者昵称');
                $table->integer('parent_id')->comment('推荐者id');
                $table->string('parent_name')->nullable()->comment('推荐者昵称');
                $table->string('shop_name')->nullable()->comment('商家名称');
                $table->string('order_sn')->nullable()->comment('订单号');
                $table->string('order_id')->nullable()->comment('订单号');
                $table->decimal('price', 14)->nullable()->comment('订单金额');
                $table->decimal('cost_price', 14)->nullable()->comment('订单成本价');
                $table->decimal('dispatch_price', 14)->nullable()->comment('订单运费');
                $table->decimal('undividend', 14)->nullable()->comment('未被分红利润');
                $table->decimal('supplier', 14)->nullable()->comment('供应商利润');
                $table->decimal('cashier', 14)->nullable()->comment('收银台利润');
                $table->decimal('store', 14)->nullable()->comment('门店利润');
                $table->decimal('point', 14)->nullable()->comment('获得积分');
                $table->decimal('love', 14)->nullable()->comment('获得爱心值');
                $table->decimal('micro_shop', 14)->nullable()->comment('微店分红');
                $table->decimal('team_dividend', 14)->nullable()->comment('经销商提成');
                $table->decimal('area_dividend', 14)->nullable()->comment('区域分红');
                $table->decimal('merchant', 14)->nullable()->comment('招商员分红');
                $table->decimal('merchant_center', 14)->nullable()->comment('招商中心分红');
                $table->decimal('commission', 14)->nullable()->comment('分销分红');
                $table->string('address')->nullable()->comment('地址');
                $table->integer('status')->nullable()->comment('订单状态');
                $table->integer('plugin_id')->nullable()->comment('插件id');
                $table->integer('day_time')->comment('购买日期');
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
		Schema::dropIfExists('yz_order_income_everyday');
	}

}
