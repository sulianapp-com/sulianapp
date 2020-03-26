<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/17
 * Time: 10:10
 */

namespace app\common\services\address;

use Illuminate\Support\Facades\DB;
use app\common\models\Address;
use app\common\services\Utils;

class GenerateAddressJs
{
    public function address()
    {
        if (config('app.framework') == 'platform') {
            $file_dir = base_path().'/addons/yun_shop/static';
        } else {
            $file_dir = base_path().'/static';
        }


        $b = $this->abcd();
        $b = json_encode($b, JSON_UNESCAPED_UNICODE);
        $str = 'var district ='.$b.';if (typeof define === "function") {define(district)} else {window.YDUI_DISTRICT = district}';
        $ccc =  file_put_contents($file_dir.'/gov_province_city_area_id.js', $str);
        return $ccc;

    }

    public function abcd($parentId = 0, $level = 1)
    {
        $a = Address::where('parentid', $parentId)->where('level', $level)->get()->toArray();
        if (empty($a)) return [];

        foreach ($a as $key => $value) {
            $address[$key]['v'] = $value['id'];
            $address[$key]['n'] = $value['areaname'];
            $address[$key]['c'] = $this->abcd($value['id'], ($value['level'] + 1));
        }

        return $address;
    }

}