<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberCouponTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_member_coupon')) {
            Schema::create('yz_member_coupon', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid');
                $table->string('uid')->nullable()->default('');
                $table->integer('coupon_id')->nullable()->default(0)->index('idx_couponid');
                $table->boolean('get_type')->nullable()->default(0)->index('idx_gettype');
                $table->integer('used')->nullable()->default(0);
                $table->integer('use_time')->nullable()->default(0);
                $table->integer('get_time')->nullable()->default(0);
                $table->integer('send_uid')->nullable()->default(0);
                $table->string('order_sn')->nullable()->default('');
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
        Schema::dropIfExists('yz_member_coupon');
    }

}
