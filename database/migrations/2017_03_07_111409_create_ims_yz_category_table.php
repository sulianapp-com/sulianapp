<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzCategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_category')) {
            Schema::create('yz_category', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid');
                $table->string('name', 50)->nullable();
                $table->string('thumb')->nullable();
                $table->integer('parent_id')->nullable()->default(0)->index('idx_parentid');
                $table->string('description', 500)->nullable();
                $table->boolean('display_order')->nullable()->default(0)->index('idx_displayorder');
                $table->boolean('enabled')->nullable()->default(1)->index('idx_enabled');
                $table->boolean('is_home')->nullable()->default(0)->index('idx_ishome');
                $table->string('adv_img')->nullable()->default('');
                $table->string('adv_url', 500)->nullable()->default('');
                $table->boolean('level')->nullable()->default(0);
                $table->string('advimg_pc')->default('');
                $table->string('advurl_pc', 500)->default('');
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
		Schema::dropIfExists('yz_category');
	}

}
