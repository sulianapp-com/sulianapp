<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzMemberMonthRank extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_member_month_rank')) {
            Schema::create('yz_member_month_rank', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('member_id')->default(0);
                $table->smallInteger('year')->default(0);
                $table->smallInteger('month')->default(0);
                $table->decimal('price', 10)->default(0.00)->comment('一二级团队业绩总额');
                $table->integer('rank')->default(0);
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
