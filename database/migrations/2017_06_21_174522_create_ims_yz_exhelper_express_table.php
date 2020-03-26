<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzExhelperExpressTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_exhelper_express')) {
            Schema::create('yz_exhelper_express', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0);
                $table->boolean('type')->default(0);
                $table->string('expressname')->nullable()->default('');
                $table->string('expresscom')->nullable()->default('');
                $table->string('express')->nullable()->default('');
                $table->decimal('width', 10)->nullable()->default(0.00);
                $table->text('datas', 65535)->nullable();
                $table->decimal('height', 10)->nullable()->default(0.00);
                $table->string('bg')->nullable()->default('');
                $table->boolean('isdefault')->default(0);
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
		Schema::drop('ims_yz_exhelper_express');
	}

}
