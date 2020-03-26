<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertDataToYzPayType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_pay_type')) {
            if(!\app\common\models\PayType::find(19)){
                \app\common\models\PayType::insert([
                    'id' => 19,
                    'name' =>'EUP支付',
                    'plugin_id' =>0,
                    'code' =>'EUP',
                    'type' =>2,
                    'unit' =>'元',
                    'setting_key' =>'plugin.eup_pay',
                ]);
            }
            if(!\app\common\models\PayType::find(23)) {

                \app\common\models\PayType::insert([
                    'id' => 23,
                    'name' => 'PLD支付',
                    'plugin_id' => 0,
                    'code' => 'PLD',
                    'type' => 2,
                    'unit' => '元',
                    'setting_key' => 'plugin.pld_pay',
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
