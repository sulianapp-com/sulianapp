<?php

namespace app\common\services\plugin\leasetoy;

use app\common\components\BaseController;

/**
* Author: 芸众商城 www.yunzshop.com
* Date: 2018/3/19
*/
class LeaseToySet extends BaseController
{
    protected $leaseToy;
        

    function __construct()
    {
        parent::__construct();

        $this->leaseToy =  \Setting::get('plugin.lease_toy');
    }

    //是否开启
    public static function whetherEnabled()
    {
        $leaseToy = \Setting::get('plugin.lease_toy');

        if (app('plugins')->isEnabled('lease-toy')) {
            if ($leaseToy['is_lease_toy']) {
                return $leaseToy['is_lease_toy'];
            }
        }

        return 0;
    }
}