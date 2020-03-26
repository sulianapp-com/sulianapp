<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateToImsYzGoodsSaleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \app\frontend\models\goods\Sale::where('ed_money',0)->update(['ed_money'=>'']);
        \app\frontend\models\goods\Sale::where('ed_num',0)->update(['ed_num'=>'']);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
