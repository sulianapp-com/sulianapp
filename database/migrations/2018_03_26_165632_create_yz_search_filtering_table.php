<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzSearchFilteringTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_search_filtering')) {
            Schema::create('yz_search_filtering', function(Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid');
                $table->string('name', 50)->nullable()->comment('过滤名称');
                $table->integer('parent_id')->nullable()->default(0);
                $table->string('value')->unllable()->default(0)->comment('值');
                $table->integer('grade')->unllable()->default(0)->comment('等级');
                $table->tinyInteger('is_show')->unllable()->default(0);

                $table->integer('updated_at')->nullable();
                $table->integer('created_at')->nullable();
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
        Schema::dropIfExists('yz_search_filtering');
    }
}
