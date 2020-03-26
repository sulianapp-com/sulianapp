<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-10-21
 * Time: 15:04
 */

namespace app\common\modules\site;


class WqUniSetting
{
    private $wqUniSettingModel;

    public function __construct()
    {
        $this->wqUniSettingModel = \app\common\models\WqUniSetting::uniacid()->get();
    }

    public function getSetting()
    {
        return $this->wqUniSettingModel;
    }

    public function get()
    {
        return $this->getSetting()->first();
    }
}