<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberIncomeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_member_income')) {
            Schema::create('yz_member_income', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid');
                $table->integer('member_id');
                $table->string('type', 60)->default('');
                $table->integer('type_id')->nullable();
                $table->string('type_name', 120)->nullable();
                $table->decimal('amount', 14)->default(0.00);
                $table->boolean('status')->default(0);
                $table->text('detail', 65535)->nullable();
                $table->string('create_month', 20)->nullable()->default('');
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
		Schema::dropIfExists('yz_member_income');
	}

}
