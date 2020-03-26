<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCashPayToPayTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Schema::hasTable('yz_pay_type')) {
            if (!\app\common\models\PayType::whereCode('cashPay')->count()) {
                \app\common\models\PayType::insert(['name' => '现金支付', 'code' => 'cashPay', 'type' => 1, 'plugin_id' => 0, 'unit' => '元']);
                \app\common\models\PayType::where('code', 'cashPay')->update(['id' => 8]);
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
        if (\Schema::hasTable('yz_pay_type')) {
            \app\common\models\PayType::where('code', 'cashPay')->delete();
        }
    }
}
