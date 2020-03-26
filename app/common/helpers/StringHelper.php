<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 22/02/2017
 * Time: 15:14
 */

namespace app\common\helpers;


use Illuminate\Support\Str;

class StringHelper extends Str
{
    /**
     * 驼峰字符分隔
     * 如： camelCaseToSplit => camel-case-to-split
     *
     * @param $string
     * @param string $split 分隔符默认 -
     * @return string
     */
    public static function camelCaseToSplit($string, $split = '-')
    {
        return strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', $split, $string));
    }

    /**
     * 分隔符转驼峰
     * 如： camel-case-to-split => camelCaseToSplit
     *
     * @param $string
     * @param string $split
     * @return mixed
     */
    public static function splitToCamelCase($string, $split = '-')
    {
        return preg_replace_callback(
            "/(" . $split . "([a-z]))/",
            function ($match) {
                return strtoupper($match[2]);
            },
            $string
        );
    }


}