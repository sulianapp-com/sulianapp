<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDataToPayType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Schema::hasTable('yz_pay_type')) {
            if(\app\common\models\PayType::whereName('后台付款')->count()){
                return;
            }
            \app\common\models\PayType::insert(['name' => '后台付款', 'code' => 'backend', 'type' => 1, 'plugin_id' => 0, 'unit' => '元']);
            \app\common\models\PayType::where('code', 'backend')->update(['id' => 5]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (\Schema::hasTable('yz_pay_type')) {
            \app\common\models\PayType::where('code', 'backend')->delete();
        }
    }
}
