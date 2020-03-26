<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzTemplateMessageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_template_message')) {
            Schema::create('yz_template_message', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('type', 20)->nullable()->default('system');
                $table->string('item', 45);
                $table->string('parent_item', 45)->default('');
                $table->string('title', 45);
                $table->string('template_id_short', 45);
                $table->string('template_id', 45);
                $table->string('content');
                $table->string('example');
                $table->boolean('status')->default(0);
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
		Schema::dropIfExists('yz_template_message');
	}

}
