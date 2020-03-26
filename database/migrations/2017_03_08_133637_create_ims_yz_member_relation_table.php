<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberRelationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_member_relation')) {
            Schema::create('yz_member_relation', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable();
                $table->boolean('status')->default(0);
                $table->boolean('become')->default(0);
                $table->boolean('become_order')->default(0);
                $table->boolean('become_child')->default(0);
                $table->integer('become_ordercount')->nullable()->default(0);
                $table->decimal('become_moneycount', 14,2)->nullable()->default(0.00);
                $table->integer('become_goods_id')->nullable()->default(0);
                $table->boolean('become_info')->default(1);
                $table->boolean('become_check')->default(1);
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
		Schema::dropIfExists('yz_member_relation');
	}

}
