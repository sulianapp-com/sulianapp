<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RepairDataToYzWithdraw extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $error_parameter = 'Yunshop\AreaDividend\models\TeamDividendModel';
        $right_parameter = 'Yunshop\TeamDividend\models\TeamDividendModel';

        \app\common\models\Withdraw::whereType($error_parameter)->update(['type' => $right_parameter]);
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
