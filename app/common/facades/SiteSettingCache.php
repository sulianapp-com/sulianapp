<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 01/03/2017
 * Time: 12:51
 */

namespace app\common\facades;


use Illuminate\Support\Facades\Facade;
use app\common\models\Setting as SettingModel;

class SiteSettingCache extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'siteSettingCache';
    }
}