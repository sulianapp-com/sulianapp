<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeletedAtToImsYzMemberCoupon extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_member_coupon')) {
            Schema::table('yz_member_coupon', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_member_coupon', 'deleted_at')) {
                    $table->integer('deleted_at')->nullable();
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
        if (Schema::hasTable('yz_member_coupon')) {
            Schema::table('yz_member_coupon', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }
    }
}
