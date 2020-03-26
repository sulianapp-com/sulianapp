<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUpgradeAtToYzMemberTable extends Migration
{

    public function up()
    {
        if (Schema::hasTable('yz_member')) {
            Schema::table('yz_member', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_member', 'upgrade_at')) {
                    $table->integer('upgrade_at')->nullable()->comment('升级时间');
                    $table->integer('downgrade_at')->nullable()->comment('降级时间');
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
        //
    }
}
