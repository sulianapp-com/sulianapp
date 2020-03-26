<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/17
 * Time: 下午2:40
 */

namespace app\frontend\modules\coin;


use app\common\models\VirtualCoin;

class InvalidVirtualCoin extends VirtualCoin
{
    public $code = 'invalid';
    public $name = '无';


    protected function _getCode()
    {
        return $this->code;
    }

    protected function _getExchangeRate()
    {
        return 1;
    }

    protected function _getName()
    {
        return $this->name;
    }
}