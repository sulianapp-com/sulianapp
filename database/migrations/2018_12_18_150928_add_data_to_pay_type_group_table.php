<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDataToPayTypeGroupTable extends Migration
{
    /**
     * 对支付方式组进行创建，这里暂时先对微信，支付宝，余额，后台这四种方式创建分组
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_pay_type_group')) {
            if(!\app\common\models\PayTypeGroup::find(1)){
                \app\common\models\PayTypeGroup::insert([
                    'id' => 1,
                    'name' =>'微信支付',
                ]);
            }
            if(!\app\common\models\PayTypeGroup::find(2)) {
                \app\common\models\PayTypeGroup::insert([
                    'id' => 2,
                    'name' =>'支付宝支付',
                ]);
            }
            if(!\app\common\models\PayTypeGroup::find(3)) {
                \app\common\models\PayTypeGroup::insert([
                    'id' => 3,
                    'name' =>'余额支付',
                ]);
            }
            if(!\app\common\models\PayTypeGroup::find(4)) {
                \app\common\models\PayTypeGroup::insert([
                    'id' => 4,
                    'name' =>'后台付款',
                ]);
            }
            if(!\app\common\models\PayTypeGroup::find(5)) {
                \app\common\models\PayTypeGroup::insert([
                    'id' => 5,
                    'name' =>'现金支付',
                ]);
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
