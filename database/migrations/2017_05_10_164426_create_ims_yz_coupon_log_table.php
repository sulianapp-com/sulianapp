<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzCouponLogTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_coupon_log')) {
            Schema::create('yz_coupon_log', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid');
                $table->string('logno')->nullable()->default('');
                $table->string('member_id')->nullable()->default('');
                $table->integer('couponid')->nullable()->default(0)->index('idx_couponid');
                $table->boolean('paystatus')->nullable()->default(0)->index('idx_paystatus');
                $table->boolean('creditstatus')->nullable()->default(0);
                $table->boolean('paytype')->nullable()->default(0);
                $table->boolean('getfrom')->nullable()->default(0)->index('idx_getfrom');
                $table->integer('status')->nullable()->default(0)->index('idx_status');
                $table->integer('createtime')->nullable()->default(0)->index('idx_createtime');
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
        Schema::dropIfExists('yz_coupon_log');
    }

}
