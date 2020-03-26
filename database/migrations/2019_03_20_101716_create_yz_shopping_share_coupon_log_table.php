<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzShoppingShareCouponLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_shopping_share_coupon_log')) {
            Schema::create('yz_shopping_share_coupon_log', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0)->nullable();
                $table->integer('share_uid')->default(0)->nullable()->comment('分享者会员id');
                $table->integer('receive_uid')->default(0)->nullable()->comment('领取者会员id');
                $table->integer('share_coupon_id')->default(0)->nullable()->comment('优惠卷分享表id');
                $table->integer('order_id')->default(0)->nullable()->comment('订单ID');
                $table->integer('coupon_id')->default(0)->nullable()->comment('优惠卷id');
                $table->string('coupon_name')->nullable()->comment('优惠卷名称');
                $table->text('log')->nullable()->comment('日志详细');
                $table->string('remark')->nullable()->comment('字段预留');
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
        Schema::dropIfExists('yz_shopping_share_coupon_log');
    }
}
