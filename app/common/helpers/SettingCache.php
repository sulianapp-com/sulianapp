<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/9/14
 * Time: 下午5:30
 */

namespace app\common\helpers;


use app\common\facades\Setting;

class SettingCache
{
    private $settingCollection;
    private $expiredTimestamp;

    public function loadSettingFromCache($uniacid)
    {
        $this->settingCollection[$uniacid] = \Cache::get($uniacid . '_setting') ?: [];
        $this->expiredTimestamp[$uniacid] = time() + 20;
    }

    public function getSetting()
    {
        if (!$this->settingCollection || !in_array(Setting::$uniqueAccountId, array_keys($this->settingCollection))) {
            $this->loadSettingFromCache(Setting::$uniqueAccountId);
        }
        if (time() > $this->expiredTimestamp[Setting::$uniqueAccountId]) {
            $this->loadSettingFromCache(Setting::$uniqueAccountId);
        }
        return $this->settingCollection[Setting::$uniqueAccountId];

    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return array_has($this->getSetting(), $key);
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return array_get($this->getSetting(), $key, $default);

    }

    public function push($key, $value, $minutes = null)
    {
        $arrayValue = $this->get($key, []);
        $arrayValue[] = $value;
        $this->put($key, $arrayValue, $minutes);
    }

    /**
     * @param $key
     * @param $value
     * @param null $minutes
     * @return mixed
     */
    public function put($key, $value, $minutes = null)
    {
        $this->loadSettingFromCache(Setting::$uniqueAccountId);
        $setting = $this->getSetting();
        yz_array_set($setting, $key, $value);
        \Cache::put(Setting::$uniqueAccountId . '_setting', $setting, $minutes);

        $this->loadSettingFromCache(Setting::$uniqueAccountId);
    }


}