<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzAddressTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_address')) {
            Schema::create('yz_address', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('areaname')->nullable();
                $table->integer('parentid')->nullable();
                $table->integer('level')->nullable();
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
		Schema::dropIfExists('yz_address');
	}

}
