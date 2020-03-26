<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCloudPayToPayTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Schema::hasTable('yz_pay_type')) {
            if(!\app\common\models\PayType::whereCode('cloudPayWechat')->count()){
                \app\common\models\PayType::insert(['name' => '云收银微信', 'code' => 'cloudPayWechat', 'type' => 1, 'plugin_id' => 0, 'unit' => '元']);
                \app\common\models\PayType::where('code', 'cloudPayWechat')->update(['id' => 6]);
            }
            if(!\app\common\models\PayType::whereCode('cloudPayAlipay')->count()){
                \app\common\models\PayType::insert(['name' => '云收银支付宝', 'code' => 'cloudPayAlipay', 'type' => 1, 'plugin_id' => 0, 'unit' => '元']);
                \app\common\models\PayType::where('code', 'cloudPayAlipay')->update(['id' => 7]);
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
            \app\common\models\PayType::where('code', 'cloudPayWechat')->delete();
            \app\common\models\PayType::where('code', 'cloudPayAlipay')->delete();
        }
    }
}
