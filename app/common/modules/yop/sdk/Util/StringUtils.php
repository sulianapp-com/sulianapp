<?php

/**
 * Created by PhpStorm.
 * User: yp-tc-7176
 * Date: 17/7/16
 * Time: 20:12
 */


namespace app\common\modules\yop\sdk\Util;

abstract class StringUtils
{


    static function isBlank($field)
    {

        if ($field == '') {
            return false;
        } else {
            return true;
        }

    }
}