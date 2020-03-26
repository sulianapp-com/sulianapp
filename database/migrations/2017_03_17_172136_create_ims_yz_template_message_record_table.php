<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzTemplateMessageRecordTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_template_message_record')) {
            Schema::create('yz_template_message_record', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable();
                $table->string('member_id', 20);
                $table->char('openid', 32)->default('');
                $table->string('template_id', 45);
                $table->string('url')->default('');
                $table->char('top_color', 7)->default('');
                $table->text('data', 65535);
                $table->integer('created_at')->default(0);
                $table->integer('updated_at')->default(0);
                $table->integer('send_time')->default(0);
                $table->boolean('status')->default(0);
                $table->string('msgid', 20)->nullable()->default('');
                $table->boolean('result')->default(0);
                $table->integer('wechat_send_at')->default(0);
                $table->boolean('sended_count')->default(1);
                $table->text('extend_data', 65535)->nullable();
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
		Schema::dropIfExists('yz_template_message_record');
	}

}
