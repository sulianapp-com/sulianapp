<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDispatchClassify extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_dispatch_classify')) {
            Schema::create('yz_dispatch_classify', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid');
                $table->string('dispatch_id')->nullable()->comment('');
                $table->tinyInteger('freight_type')->comment('运费类型');
                $table->integer('freight_value')->nullable()->comment('固定运费值');
                $table->integer('template_id')->nullable()->comment('运费模板ID');
                $table->integer('is_cod')->comment("是否支持貨到付款");
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
        Schema::dropIfExists('dispatch_classify');
    }
}
