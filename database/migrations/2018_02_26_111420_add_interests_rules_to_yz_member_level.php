<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInterestsRulesToYzMemberLevel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         if (Schema::hasTable('yz_member_level')) {
            Schema::table('yz_member_level',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_member_level',
                        'freight_reduction')) {
                        $table->string('freight_reduction', 45)->nullable()->comment('运费减免');
                    }
                    if (!Schema::hasColumn('yz_member_level',
                        'interests_rules')) {
                        $table->text('interests_rules', 65535)->nullable()->comment('会员权益细则');
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
