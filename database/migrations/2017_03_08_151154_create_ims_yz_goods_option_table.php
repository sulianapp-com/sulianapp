<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzGoodsOptionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_goods_option')) {
            Schema::create('yz_goods_option', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid');
                $table->integer('goods_id')->nullable()->default(0)->index('idx_goodsid');
                $table->string('title', 50)->nullable();
                $table->string('thumb', 60)->nullable();
                $table->integer('product_price')->nullable()->default(0);
                $table->integer('market_price')->nullable()->default(0);
                $table->integer('cost_price')->nullable()->default(0);
                $table->integer('stock')->nullable()->default(0);
                $table->decimal('weight', 10)->nullable()->default(0.00);
                $table->integer('display_order')->nullable()->default(0)->index('idx_displayorder');
                $table->text('specs', 65535)->nullable();
                $table->string('skuId')->nullable()->default('');
                $table->string('goods_sn')->nullable()->default('');
                $table->string('product_sn')->nullable()->default('');
                $table->integer('virtual')->nullable()->default(0);
                $table->string('red_price', 50)->nullable()->default('');
                $table->integer('created_at')->nullable();
                $table->integer('deleted_at')->nullable();
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
		Schema::dropIfExists('yz_goods_option');
	}

}
