<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzCouponTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_coupon')) {
            Schema::create('yz_coupon', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid');
                $table->integer('cat_id')->nullable()->default(0);
                $table->string('name')->nullable()->default('');
                $table->boolean('get_type')->nullable()->default(0);
                $table->integer('level_limit')->nullable();
                $table->integer('get_max')->nullable()->default(0);
                $table->boolean('use_type')->nullable()->default(0);
                $table->string('bgcolor')->nullable()->default('');
                $table->integer('enough')->unsigned()->nullable()->default(0);
                $table->boolean('coupon_type')->nullable()->default(0);
                $table->boolean('time_limit')->nullable()->default(0);
                $table->integer('time_days')->nullable()->default(0);
                $table->integer('time_start')->nullable()->default(0);
                $table->integer('time_end')->nullable()->default(0);
                $table->boolean('coupon_method')->nullable();
                $table->decimal('deduct', 10)->nullable()->default(0.00);
                $table->decimal('discount', 10)->nullable()->default(0.00);
                $table->string('thumb')->nullable()->default('');
                $table->text('desc', 65535)->nullable();
                $table->integer('total')->nullable()->default(0);
                $table->boolean('status')->nullable()->default(0);
                $table->text('resp_desc', 65535)->nullable();
                $table->string('resp_thumb')->nullable()->default('');
                $table->string('resp_title')->nullable()->default('');
                $table->string('resp_url')->nullable()->default('');
                $table->string('remark', 1000)->nullable()->default('');
                $table->integer('display_order')->nullable()->default(0);
                $table->integer('supplier_uid')->nullable()->default(0);
                $table->text('cashiersids', 65535)->nullable();
                $table->text('cashiersnames', 65535)->nullable();
                $table->text('category_ids', 65535)->nullable();
                $table->text('categorynames', 65535)->nullable();
                $table->text('goods_names', 65535)->nullable();
                $table->text('goods_ids', 65535)->nullable();
                $table->text('supplierids', 65535)->nullable();
                $table->text('suppliernames', 65535)->nullable();
                $table->integer('createtime')->nullable()->default(0);
                $table->integer('created_at')->unsigned()->nullable();
                $table->integer('updated_at')->unsigned()->nullable();
                $table->integer('deleted_at')->unsigned()->nullable();
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
        Schema::dropIfExists('yz_coupon');
    }

}
