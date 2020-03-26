<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-07-26
 * Time: 11:49
 */

namespace app\platform\Repository;



class SystemSetting
{
    private $systemModel;

    public function __construct()
    {
        $this->systemModel = \app\platform\modules\system\models\SystemSetting::get();
    }

    public function getSetting()
    {
        return $this->systemModel;
    }

    public function get($key)
    {
        return $this->getSetting()->where('key', $key)->first();
    }
}