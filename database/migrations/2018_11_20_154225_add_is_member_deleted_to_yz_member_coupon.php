<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsMemberDeletedToYzMemberCoupon extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_member_coupon')) {
            if (!Schema::hasColumn('yz_member_coupon', 'is_member_deleted')) {
                Schema::table('yz_member_coupon', function (Blueprint $table) {
                    $table->tinyInteger('is_member_deleted')->unsigned()->default(0);
                });
            }
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
