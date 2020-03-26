<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzShoppingShareCouponTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_shopping_share_coupon')) {
            Schema::create('yz_shopping_share_coupon', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0)->nullable();
                $table->integer('member_id')->default(0)->nullable()->comment('分享者会员id');
                $table->integer('order_id')->default(0)->nullable()->comment('订单id');
                $table->text('share_coupon')->nullable()->comment('分享优惠卷集合');
                $table->text('receive_coupon')->nullable()->comment('领取优惠卷集合');
                $table->tinyInteger('obtain_restriction')->default(0)->nullable()->comment('领取限制');
                $table->tinyInteger('status')->default(0)->nullable()->comment('是否以领完');
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
        Schema::dropIfExists('yz_shopping_share_coupon');
    }
}
