<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRelationIdYzMemberRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $relation = \app\backend\modules\member\models\MemberRelation::groupBy('uniacid')
            ->distinct('uniacid')
            ->orderBy('id','desc')
            ->pluck('id')
            ->toArray();

        \app\backend\modules\member\models\MemberRelation::whereNotIn('id', $relation)->delete();

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
