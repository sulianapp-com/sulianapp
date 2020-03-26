<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateYzMemberMonthOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (Schema::hasTable('yz_member_month_order')) {
            if (Schema::hasColumn('yz_member_month_order', 'member_id')) {
                try {
                    Schema::table('yz_member_month_order', function (Blueprint $table) {
                        $table->integer('member_id')->default(0)->index('idx_member_id')->change();
                    });
                } catch (\Exception $e) {
                    
                }
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
