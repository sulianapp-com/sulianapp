<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class YzMemberMiniFormid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_member_mini_formid')) {
            Schema::create('yz_member_mini_formid', function (Blueprint $table) {
                $table->increments('id');
                $table->string('formid');
                $table->bigInteger('addtime');
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
        Schema::dropIfExists('yz_member_mini_formid');
    }
}
