<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberUniqueTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_member_unique')) {
            Schema::create('yz_member_unique', function (Blueprint $table) {
                $table->integer('unique_id', true);
                $table->integer('uniacid')->nullable()->index('idx_uniacid');
                $table->string('unionid', 64)->index('idx_unionid');
                $table->integer('member_id')->index('idx_member_id');
                $table->string('type')->nullable();
                $table->integer('created_at')->unsigned()->default(0);
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
		Schema::dropIfExists('yz_member_unique');
	}

}
