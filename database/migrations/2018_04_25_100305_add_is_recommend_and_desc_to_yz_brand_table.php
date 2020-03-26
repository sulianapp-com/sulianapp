<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsRecommendAndDescToYzBrandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_brand')) {
            Schema::table('yz_brand', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_brand', 'is_recommend')) {
                    $table->tinyInteger('is_recommend')->unllable()->default(0)->commet('是否推荐 0：否 1：是');

                }

                if (Schema::hasColumn('yz_brand', 'desc')) {
                    $table->text('desc', 65535)->nullable()->change();
                } else {
                    $table->text('desc', 65535)->nullable();
                }
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
        if (Schema::hasTable('yz_brand')) {
            Schema::table('yz_brand', function (Blueprint $table) {
                $table->dropColumn('is_recommend');
            });
        }
    }
}
