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

class Setting extends Facade
{
    public static $uniqueAccountId = 0;

    private static $instance;

    protected static function getFacadeAccessor()
    {
        return 'setting';
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new SettingModel();
        }
        return self::$instance;
    }


    /**
     * 设置配置信息
     *
     * @param $key
     * @param null $value
     */
    public static function set($key, $value = null)
    {
        return self::getInstance()->setValue(self::$uniqueAccountId, $key, $value);
    }

    /**
     * 设置不区分公众号配置信息
     *
     * @param $key
     * @param null $value
     * @return mixed
     */
    public static function setNotUniacid($key, $value = null)
    {
        return self::getInstance()->setValue(0, $key, $value);
    }

    /**
     * 获取配置信息
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        return self::getInstance()->getValue(self::$uniqueAccountId, $key, $default);
    }

    /**
     * 获取不区分公众号配置信息
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public static function getNotUniacid($key, $default = null)
    {
        return self::getInstance()->getValue(0, $key, $default);
    }

    /**
     * 检测是否存在分组
     * @param $group
     * @return bool
     */
    public static function exitsGroup($group)
    {
        return self::getInstance()->exists(self::$uniqueAccountId, $group);
    }

    /**
     * 获取分组所有值
     * @param $group
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getAllByGroup($group)
    {
        return self::getInstance()->fetchSettings(self::$uniqueAccountId, $group);
    }
    /**
     * 获取分组所有值
     * @param $group
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getByGroup($group)
    {
        return self::getInstance()->getItems(self::$uniqueAccountId, $group);
    }
}