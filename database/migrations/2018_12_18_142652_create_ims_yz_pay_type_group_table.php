<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzPayTypeGroupTable extends Migration
{
    /**
     * 创建支付方式分组表
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_pay_type_group')) {
            Schema::create('yz_pay_type_group', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 100)->default('');
                $table->timestamps();
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
        Schema::dropIfExists('yz_pay_type_group');
    }
}
