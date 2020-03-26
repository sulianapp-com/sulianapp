<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzCommentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_comment')) {
            Schema::create('yz_comment', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->default(0)->index('idx_uniacid');
                $table->integer('order_id')->nullable()->default(0)->index('idx_orderid');
                $table->integer('goods_id')->default(0)->index('idx_goodsid');
                $table->integer('uid')->nullable()->default(0)->index('idx_openid');
                $table->string('nick_name', 50)->nullable()->default('');
                $table->string('head_img_url')->nullable()->default('');
                $table->string('content')->default('');
                $table->boolean('level')->nullable()->default(0);
                $table->text('images', 65535)->nullable();
                $table->boolean('deleted')->nullable()->default(0);
                $table->integer('comment_id')->nullable()->default(0);
                $table->integer('reply_id')->nullable()->default(0);
                $table->string('reply_name', 50)->nullable();
                $table->integer('created_at')->default(0)->index('idx_createtime');
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
		Schema::dropIfExists('yz_comment');
	}

}
