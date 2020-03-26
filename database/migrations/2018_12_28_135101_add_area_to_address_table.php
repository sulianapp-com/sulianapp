<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAreaToAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //山东省潍坊市所在地区少了一个新区'高新区'
        $ret = \app\common\models\Address::where('id', '370700')->first();
        if ($ret) {
            $area = \app\common\models\Address::where('level', '3')->where('parentid', '370700')->where('areaname', '高新区')->first();
            if (empty($area)) {
                \app\common\models\Address::insert([
                    'areaname' => '高新区',
                    'parentid' => 370700,
                    'level'    => 3
                ]);
            }
        }
        \app\common\models\Address::where('id', '210112')->where('areaname', '东陵区')->update(['areaname' => '浑南区']);
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
