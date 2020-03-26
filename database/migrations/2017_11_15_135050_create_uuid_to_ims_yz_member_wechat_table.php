<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUuidToImsYzMemberWechatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_member_wechat')) {
            Schema::table('yz_member_wechat',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_member_wechat',
                        'uuid')
                    ) {
                        $table->string('uuid','50')->nullable()->default('');
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
        Schema::table('yz_member_wechat', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
}
