<?php

namespace app\common\modules\site;


class SiteSettingCache
{
    public function load()
    {
        $setting = \app\common\models\SiteSetting::first();

        if (!$setting) {
            $setting = \app\common\models\SiteSetting::create(['value'=>[]]);
        }

        \Cache::put('siteSetting', $setting->value?:[], 600);
        return true;
    }

    public function get()
    {
        $cache = \Cache::get('siteSetting');
        if (!isset($cache)) {
            $this->load();
        }

        return \Cache::get('siteSetting');
    }

    public function put($key, $value)
    {
        // 获取修改后的全部设置
        $value = yz_array_set($this->get(), $key, $value);
        // 保存到缓存
        \Cache::put('siteSetting', $value, 600);
        // 同步到数据库
        $setting = \app\common\models\SiteSetting::first();
        if (!$setting) {
            $setting = \app\common\models\SiteSetting::create();
        }
        $setting->value = $value;
        return $setting->save();
    }
}