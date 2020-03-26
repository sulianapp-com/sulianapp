<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzDispatchTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_dispatch')) {
            Schema::create('yz_dispatch', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->nullable();
                $table->string('dispatch_name', 50)->nullable();
                $table->integer('display_order')->nullable();
                $table->boolean('enabled')->nullable();
                $table->boolean('is_default')->nullable();
                $table->boolean('calculate_type')->nullable();
                $table->text('areas', 65535)->nullable();
                $table->integer('first_weight')->nullable();
                $table->integer('another_weight')->nullable();
                $table->decimal('first_weight_price', 14)->nullable();
                $table->decimal('another_weight_price', 14)->nullable();
                $table->integer('first_piece')->nullable();
                $table->integer('another_piece')->nullable();
                $table->integer('first_piece_price')->nullable();
                $table->integer('another_piece_price')->nullable();
                $table->text('weight_data')->nullable();
                $table->text('piece_data')->nullable();
                $table->boolean('is_plugin')->nullable()->default(0);
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
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
		Schema::dropIfExists('yz_dispatch');
	}

}
