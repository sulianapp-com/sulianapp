<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/30
 * Time: 9:21
 */

namespace app\common\services\popularize;



class PortType
{
    /**
     * 微信
     */
    const TYPE_WEACHAT = 1;

    /**
     * 微信小程序
     */
    const TYPE_MINI = 2;

    /**
     * 手机浏览器
     */
    const TYPE_WAP = 5;

    /**
     * APP
     */
    const TYPE_APP = 7;


    /**
     * 支付宝
     */
    const TYPE_ALIPAY = 8;


    /**
     * 判断当前浏览器类型
     * @param null $type
     * @return null|string
     */
    public static function determineType($type = null)
    {
        switch ($type) {
            case self::TYPE_WEACHAT:
                $className = 'wechat';
                break;
            case self::TYPE_MINI:
                $className = 'mini';
                break;
            case self::TYPE_WAP:
                $className = 'wap';
                break;
            case self::TYPE_APP:
                $className = 'app';
                break;
            case self::TYPE_ALIPAY:
                $className = 'alipay';
                break;
            default:
                $className = null;
        }

        return $className;
    }

    /**
     * 是否显示前端推广按钮
     * @param $type integer 端口类型
     * @return bool bool  1：是 0：否
     */
    public static function popularizeShow($type)
    {
        $type = self::determineType($type);

        if ($type) {
            $info = \Setting::get('popularize.'.$type);
            if (isset($info['popularize']) && $info['popularize'] == 1) {
                return 0;
            }
        }

        return 1;
    }

    /**
     * 推广页面插件显示
     * @param $type integer 端口类型
     * @return array 端口不显示插件前端路由
     */
    public static function popularizeSet($type)
    {
        $type = self::determineType($type);

        if ($type) {
            $info = \Setting::get('popularize.'.$type);
            if (isset($info['vue_route'])) {
                return $info['vue_route'];
            }
        }

        return array();
    }
}