<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAppToPayTypeYangmingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Schema::hasTable('yz_pay_type')) {
            if (!\app\common\models\PayType::whereCode('WechatApp')->count()) {
                \app\common\models\PayType::insert(['name' => '微信App', 'code' => 'WechatApp', 'type' => 1, 'plugin_id' => 0, 'unit' => '元']);
                \app\common\models\PayType::where('code', 'WechatApp')->update(['id' => 9]);
            }
            if (!\app\common\models\PayType::whereCode('AlipayApp')->count()) {
                \app\common\models\PayType::insert(['name' => '支付宝App', 'code' => 'WechatApp', 'type' => 1, 'plugin_id' => 0, 'unit' => '元']);
                \app\common\models\PayType::where('code', 'AlipayApp')->update(['id' => 10]);
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
