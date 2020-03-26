<?php

namespace app\common\services\address;
use app\common\models\Address;

/**
* 
*/
class UrbanDistricictAddress
{
    public static $completion = [
        [
            'areaname' => '三亚市',    //海南 三亚 三亚
            'parentid' => 460200,
        ],
        [
            'areaname' => '三沙市',    //海南 三沙市 三沙市
            'parentid' => 460300,
        ],
        [
            'areaname' => '五指山市',    //海南 五指山市 五指山市
            'parentid' => 469001,
        ],
        [
            'areaname' => '琼海市',    //海南 琼海市 琼海市
            'parentid' => 469002,
        ],
        [
            'areaname' => '儋州市',    //海南 儋州市 儋州市
            'parentid' => 469003,
        ],
        [
            'areaname' => '文昌市',    //海南 文昌市 文昌市
            'parentid' => 469005,
        ],
        [
            'areaname' => '万宁市',    //海南 万宁市 万宁市
            'parentid' => 469006,
        ],
        [
            'areaname' => '东方市',    //海南 东方市 东方市
            'parentid' => 469007,
        ],
        [
            'areaname' => '定安县',    //海南 定安县 定安县
            'parentid' => 469025,
        ],
        [
            'areaname' => '屯昌县',    //海南 屯昌县 屯昌县
            'parentid' => 469026,
        ],
        [
            'areaname' => '澄迈县',    //海南 澄迈县 澄迈县
            'parentid' => 469027,
        ],
        [
            'areaname' => '临高县',    //海南 临高县 临高县
            'parentid' => 469028,
        ],
        [
            'areaname' => '白沙黎族自治县',    //海南 白沙黎族自治县 白沙黎族自治县
            'parentid' => 469030,
        ],
        [
            'areaname' => '昌江黎族自治县',    //海南 昌江黎族自治县 昌江黎族自治县
            'parentid' => 469031,
        ],
        [
            'areaname' => '乐东黎族自治县',    //海南 乐东黎族自治县 乐东黎族自治县
            'parentid' => 469033,
        ],
        [
            'areaname' => '陵水黎族自治县',    //海南 陵水黎族自治县 陵水黎族自治县
            'parentid' => 469034,
        ],
        [
            'areaname' => '保亭黎族苗族自治县',    //海南 保亭黎族苗族自治县 保亭黎族苗族自治县
            'parentid' => 469035,
        ],
        [
            'areaname' => '琼中黎族苗族自治县',    //海南 琼中黎族苗族自治县 琼中黎族苗族自治县
            'parentid' =>  469036,
        ],

        [
            'areaname' => '东莞市',    //广州 东莞市 东莞市
            'parentid' =>  441900,
        ],
        [
            'areaname' => '中山市',    //广州 中山市 中山市
            'parentid' =>  442000,
        ],

        [
            'areaname' => '济源市',    //河南 济源市 济源市
            'parentid' =>  410881,
        ],

        [
            'areaname' => '仙桃市',    //湖北 仙桃市 仙桃市
            'parentid' =>  429004,
        ],
        [
            'areaname' => '潜江市',    //湖北 潜江市 潜江市
            'parentid' =>  429005,
        ],
        [
            'areaname' => '天门市',    //湖北 天门市 天门市
            'parentid' =>  429006,
        ],
        [
            'areaname' => '神农架林区',    //湖北 神农架林区 神农架林区
            'parentid' =>  429021,
        ],

        [
            'areaname' => '嘉峪关市',    //湖北 嘉峪关市 嘉峪关市
            'parentid' =>  620200,
        ],

        [
            'areaname' => '石河子市',    //新疆 石河子市 石河子市
            'parentid' =>  659001,
        ],
        [
            'areaname' => '阿拉尔市',    //新疆 阿拉尔市 阿拉尔市
            'parentid' =>  659002,
        ],
        [
            'areaname' => '图木舒克市',    //新疆 图木舒克市 图木舒克市
            'parentid' =>  659003,
        ],
        [
            'areaname' => '五家渠市',    //新疆 五家渠市 五家渠市
            'parentid' =>  659004,
        ],
    ];


    //验证街道是否存在
    public static function verification($district)
    {
        $aaa = Address::where($district)->first();
        if (is_null($aaa)) {
            Address::insert($district);
        }
    }
}