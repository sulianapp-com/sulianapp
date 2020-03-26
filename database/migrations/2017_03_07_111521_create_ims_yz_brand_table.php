<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzBrandTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_brand')) {
            Schema::create('yz_brand', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid');
                $table->string('name', 50)->nullable()->default('')->index('idx_name')->comment('品牌名称');
                $table->string('alias', 50)->nullable()->default('')->comment('品牌别名');
                $table->string('logo')->nullable()->default('')->comment('品牌logo');
                $table->string('desc')->nullable()->default('')->comment('品牌描述信息');
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
		Schema::dropIfExists('yz_brand');
	}

}
