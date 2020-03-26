<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzCategoryDiscount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (!Schema::hasTable('yz_category_discount')) {
            Schema::create('yz_category_discount', function (Blueprint $table) {
                $table->increments('id');
                $table->string('category_ids')->nullable()->comment('分类id');
                $table->integer('uniacid')->default(0);
                $table->tinyInteger('level_discount_type')->default(1)->comment('折扣类型');
                $table->tinyInteger('discount_method')->default(1)->comment('折扣方式');
                $table->text('discount_value')->nullable()->comment('折扣值');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
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
        //
    }
}
