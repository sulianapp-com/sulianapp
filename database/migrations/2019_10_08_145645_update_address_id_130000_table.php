<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddressId130000Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (Schema::hasTable('yz_address')) {

            //要删除的区
            $d = \app\common\models\Address::where( ['areaname' => '邯郸县', 'parentid' => 130400, 'level' => 3])->first();
            if ($d) {
                \app\common\models\Street::where('parentid', $d->id)->delete();
                $d->delete();
            }
            $a = [
                [
                    'type' => 'delete', //要删除的街道
                    'where' => ['areaname'=> '永年县', 'parentid'=> 130400, 'level'=> 3],
                    'street' => ['南沿村镇','小西堡乡','姚寨乡']
                ],
                [
                    'type' => 'delete',
                    'where' => ['areaname'=> '磁县', 'parentid'=> 130400, 'level'=> 3],
                    'street' => ['高臾镇','光禄镇','辛庄营乡','花官营乡','台城乡','林坛镇','南城乡']
                ],

                [
                    'type' => 'add', ///要添加的街道
                    'where' => ['areaname'=> '丛台区', 'parentid'=> 130400, 'level'=> 3],
                    'street'   => ['南沿村镇','小西堡乡','姚寨乡', '尚璧镇','南吕固乡','兼庄乡','三陵乡','黄粱梦镇'],
                ],
                [
                    'type' => 'add',
                    'where' => ['areaname'=> '邯山区', 'parentid'=> 130400, 'level'=> 3],
                    'street'   => ['高臾镇','光禄镇','辛庄营乡','花官营乡','台城乡','河沙镇镇','南堡乡','代召乡'],
                ],
                [
                    'type' => 'add',
                    'where' => ['areaname'=> '复兴区', 'parentid'=> 130400, 'level'=> 3],
                    'street'   => ['胜利桥街道','百家街道','铁路大院街道','化林路街道','庞村街道','二六七二街道','石化街道','户村镇','彭家寨乡','康庄乡','林坛镇','南城乡'],
                ],
            ];

            foreach ($a as $value) {
                $district = \app\common\models\Address::where($value['where'])->first();
                if (!is_null($district)) {
                    if ($value['type'] == 'delete') {
                        $this->delStreet($value['street'], $district);
                    } else {
                        foreach ($value['street'] as $item) {
                            \app\common\services\address\StreetAddress::verification(['areaname'=> $item, 'parentid'=> $district->id, 'level'=> 4]);
                        }
                    }
                }

            }

            //要修改名称的
            $g = [
                [ 'where' => ['areaname'=> '肥乡县', 'parentid'=> 130400, 'level'=> 3], 'new_name'=> '肥乡区'],
                ['where' => ['areaname'=> '永年县', 'parentid'=> 130400, 'level'=> 3], 'new_name'=> '永年区'],
            ];

            foreach ($g as $g_name) {
                \app\common\models\Address::where($g_name['where'])->update(['areaname'=> $g_name['new_name']]);
            }


            (new \app\common\services\address\GenerateAddressJs())->address();

        }
    }

    //删除街道
    public function delStreet($street, $district)
    {
        foreach ($street as $aaa) {
            \app\common\models\Street::where(['parentid'=> $district->id, 'areaname'=> $aaa])->delete();
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
