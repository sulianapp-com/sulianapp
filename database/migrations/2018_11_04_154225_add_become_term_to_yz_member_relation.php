<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBecomeTermToYzMemberRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_member_relation')) {
            if (!Schema::hasColumn('yz_member_relation', 'become_term')) {
                Schema::table('yz_member_relation', function (Blueprint $table) {
                    $table->string('become_term')->nullable();
                });
                $member = \app\common\models\MemberRelation::get();
                foreach ($member as $value) {
                    if ($value->become > 1) {
                        $value->become_term = serialize([$value->become => $value->become]);
                        $value->become = 2;
                        $value->save();
                    }
                }
            }
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
