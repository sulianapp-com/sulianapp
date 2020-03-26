<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrderExpress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_order_express')) {

            $companies = [
                'shunfeng' => 'SF',
                'huitongkuaidi' => 'HTKY',
                'zhongtong' => 'ZTO',
                'shentong' => 'STO',
                'yuantong' => 'YTO',
                'yunda' => 'YD',
                'youzhengguonei' => 'YZPY',
                'ems' => 'EMS',
                'tiantian' => 'HHTT',
                'youshuwuliu' => 'UC',
                'debangwuliu' => 'DBL',
                'zhaijisong' => 'ZJS',
                'tnt' => 'TNT',
                'ups' => 'UPS',
                'fedex' => 'FEDEX',
            ];
            foreach ($companies as $key => $com) {
                \app\common\models\order\Express::where('express_code', $key)->update(['express_code' => $com]);
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
