<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/11
 * Time: 17:16
 */

namespace app\frontend\modules\finance\services;


class PluginSettleService
{
    public static function create($key)
    {
        switch ($key) {
            case 'merchant':
                $class = new \Yunshop\Merchant\services\ReturnFormatService();
                break;
            case 'commission':
                $class = new \Yunshop\Commission\services\ReturnFormatService();
                break;
            case 'areaDividend':
                $class = new \Yunshop\AreaDividend\services\ReturnFormatService();
                break;
            case 'teamDividend':
                $class = new \Yunshop\TeamDividend\services\ReturnFormatService();
                break;
            default:
                $class = null;
        }

        return $class;
    }

    public static function doesIsShow()
    {
        $config = \app\backend\modules\income\Income::current()->getItems();
        foreach ($config as $key => $value) {
            $bool = self::doesIsShowAvailable($key);

            if ($bool) {
                return true;
            }
        }

        return false;
    }

    public static function  doesIsShowAvailable($key)
    {
        $bool = false;
        switch ($key) {
            case 'merchant':
                if (\Setting::get('plugin.merchant.settlement_model')) {
                    $bool = true;
                }
                break;
            case 'commission':
                if (\Setting::get('plugin.commission.settlement_model')) {
                    $bool = true;
                }
                break;
            case 'areaDividend':
                if (\Setting::get('plugin.area_dividend.settlement_model')) {
                    $bool = true;
                }
                break;
            case 'teamDividend':
                if (\Setting::get('plugin.team_dividend.settlement_model')) {
                    $bool = true;
                }
                break;
            default:
                $bool = false;
        }

        return $bool;
    }

}