<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 22/02/2017
 * Time: 16:06
 */

namespace app\common\helpers;


class ArrayHelper
{
    public static function unreliableDataToArray($data)
    {
        if (is_array($data)) {
            return $data;
        }
        if (is_array(json_decode($data, true))) {
            return json_decode($data, true);
        }

        return explode(',', $data);
    }

    //stdClass Object 转 数组
    public static function objectArray($array)
    {
        if (is_object($array)) {
            $array = (array)$array;
        }
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $array[$key] = self::objectArray($value);
            }
        }
        return $array;
    }

    /**
     * 数组驼峰转分隔
     * 如：如：['aB'=>1,['cD'=>2]]  =>  ['a_b'=>1,['c_d'=>2]]
     *
     * @param $data
     * @param string $split
     * @return array
     */
    public static function camelToSplit($data, $split = '-')
    {
        if (is_string($data)) {
            return $data;
        }

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $newValue = self::camelToSplit($value, $split);
                unset($data[$key]);
                $newKey = StringHelper::camelCaseToSplit($key);
                $data[$newKey] = $newValue;
            }
        }

        return $data;

    }

    /**
     * 分隔数组转驼峰
     * 如：['a_b'=>1,['c_d'=>2]]  =>  ['aB'=>1,['cD'=>2]]
     *
     * @param $data
     * @param string $split
     * @return array
     */
    public static function splitToCamelCase($data, $split = '-')
    {
        if (is_string($data)) {
            return $data;
        }

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $newValue = self::camelToSplit($value, $split);
                unset($data[$key]);
                $newKey = StringHelper::splitToCamelCase($key);
                $data[$newKey] = $newValue;
            }
        }

        return $data;

    }
}