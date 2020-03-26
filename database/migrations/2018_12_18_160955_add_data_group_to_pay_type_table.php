<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDataGroupToPayTypeTable extends Migration
{
    /**
     * 对支付方式进行分组，这里先对微信，支付宝，余额，后台这四类进行分组
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_pay_type')) {
            //微信支付，分组为1
            $weChatPayType = \app\common\models\PayType::find(1);
            if($weChatPayType){
                $weChatPayType->group_id = 1;
                $weChatPayType->save();
            }
            $weChatPayType = \app\common\models\PayType::find(6);
            if($weChatPayType){
                $weChatPayType->group_id = 1;
                $weChatPayType->save();
            }
            $weChatPayType = \app\common\models\PayType::find(9);
            if($weChatPayType){
                $weChatPayType->group_id = 1;
                $weChatPayType->save();
            }
            $weChatPayType = \app\common\models\PayType::find(12);
            if($weChatPayType){
                $weChatPayType->group_id = 1;
                $weChatPayType->save();
            }

            //支付宝支付，分组为2
            $alipayPayType = \app\common\models\PayType::find(2);
            if($alipayPayType){
                $alipayPayType->group_id = 2;
                $alipayPayType->save();
            }
            $alipayPayType = \app\common\models\PayType::find(7);
            if($alipayPayType){
                $alipayPayType->group_id = 2;
                $alipayPayType->save();
            }
            $alipayPayType = \app\common\models\PayType::find(10);
            if($alipayPayType){
                $alipayPayType->group_id = 2;
                $alipayPayType->save();
            }
            $alipayPayType = \app\common\models\PayType::find(15);
            if($alipayPayType){
                $alipayPayType->group_id = 2;
                $alipayPayType->save();
            }

            //余额支付，分组为3
            $balancePayType = \app\common\models\PayType::find(3);
            if($balancePayType){
                $balancePayType->group_id = 3;
                $balancePayType->save();
            }

            //后台支付，分组为4
            $backendPayType = \app\common\models\PayType::find(5);
            if($backendPayType){
                $backendPayType->group_id = 4;
                $backendPayType->save();
            }
            //现金支付，分组为5
            $backendPayType = \app\common\models\PayType::find(8);
            if($backendPayType){
                $backendPayType->group_id = 5;
                $backendPayType->save();
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
