<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShareToYzGoodsCouponTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_goods_coupon')) {
            Schema::table('yz_goods_coupon', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_goods_coupon', 'shopping_share')) {
                    $table->tinyInteger('shopping_share')->default(0)->comment('购买分享优惠券开关');
                }

                if (!Schema::hasColumn('yz_goods_coupon', 'share_coupon')) {
                    $table->text('share_coupon')->nullable()->comment('分享优惠券');
                }
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
