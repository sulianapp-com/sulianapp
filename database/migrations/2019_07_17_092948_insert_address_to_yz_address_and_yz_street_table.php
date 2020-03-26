<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertAddressToYzAddressAndYzStreetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_address')) {
            $ret = \app\common\models\Address::select()->where('areaname', '烟台市')->where('parentid', 370000)->whereLevel(2)->first();
            if ($ret) {

                if (is_null(\app\common\models\Address::where(['areaname' => '烟台高新技术产业开发区', 'parentid' => $ret['id'], 'level' => 3])->first())) {
                    \app\common\models\Address::insert(['areaname' => '烟台高新技术产业开发区', 'parentid' => $ret['id'], 'level' => 3]);
                }

                if (is_null(\app\common\models\Address::where(['areaname' => '烟台经济技术开发区', 'parentid' => $ret['id'], 'level' => 3])->first())) {
                    $ret_id = \app\common\models\Address::insertGetId(['areaname' => '烟台经济技术开发区', 'parentid' => $ret['id'], 'level' => 3]);
                    $street = ['长江路社区','海河社区','八角街道','古现街道','大季家街道'];
                    foreach ($street as $value) {
                        \app\common\services\address\StreetAddress::verification(['areaname'=> $value, 'parentid'=> $ret_id, 'level'=> 4]);
                    }
                }
            }

            (new \app\common\services\address\GenerateAddressJs())->address();

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
