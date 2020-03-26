<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMessageTemplateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_message_template')) {
            Schema::create('yz_message_template', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0);
                $table->string('title')->default('0');
                $table->string('template_id')->default('');
                $table->text('first', 65535)->nullable();
                $table->string('first_color')->nullable();
                $table->text('data', 65535)->nullable();
                $table->text('remark', 65535)->nullable();
                $table->string('remark_color')->nullable();
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
		Schema::drop('yz_message_template');
	}

}
