<?php

namespace app\common\modules\site;


class SiteSetting
{
    private $setting;


    public function loadSettingFromCache()
    {
        $this->setting = \app\common\facades\SiteSettingCache::get();
    }

    public function getSetting()
    {
        if (!isset($this->setting)) {
            $this->loadSettingFromCache();
        }

        return $this->setting;

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

    /**
     * @param $key
     * @param $value
     * @param null $minutes
     * @return mixed
     */
    public function set($key, $value)
    {
        \app\common\facades\SiteSettingCache::put($key, $value);
    }
}