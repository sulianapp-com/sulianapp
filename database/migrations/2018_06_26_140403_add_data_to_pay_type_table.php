<?php

use Illuminate\Support\Facades\Schema;
use \app\common\models\Flow;
use Illuminate\Database\Migrations\Migration;

class AddDataToPayTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_pay_type')) {
            if(!\app\common\models\PayType::find(16)){
                \app\common\models\PayType::insert([
                    'id' => 16,
                    'name' =>'汇款支付',
                    'plugin_id' =>0,
                    'code' =>'remittance',
                    'type' =>1,
                    'unit' =>'元',
                    'setting_key' =>'shop.pay.remittance',
                ]);
            }
            if(!\app\common\models\PayType::find(17)) {

                \app\common\models\PayType::insert([
                    'id' => 17,
                    'name' => '货到付款',
                    'plugin_id' => 0,
                    'code' => 'COD',
                    'type' => 1,
                    'unit' => '元',
                    'setting_key' => 'shop.pay.cod',
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

    }
}
