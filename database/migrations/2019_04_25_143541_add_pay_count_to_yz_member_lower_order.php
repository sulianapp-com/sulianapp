<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPayCountToYzMemberLowerOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_member_lower_order')) {
            Schema::table('yz_member_lower_order', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_member_lower_order', 'pay_count')) {
                    $table->integer('pay_count')->default(0)->nullable()->comment('团队支付人数');
                }
                if (!Schema::hasColumn('yz_member_lower_order', 'team_count')) {
                    $table->integer('team_count')->default(0)->nullable()->comment('团队总人数');
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
