<?php


/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/1/5
 * Time: 下午2:14
 */

namespace app\frontend\modules\order\services;

use Yunshop\VideoDemand\models\CourseGoodsModel;
use Setting;

class VideoDemandOrderGoodsService
{

    public function __construct()
    {

    }

    /**
     * 是否启用视频点播
     */
    public static function whetherEnabled()
    {

        //视频点播插件设置
        $videoDemand = Setting::get('plugin.video_demand');

        if (app('plugins')->isEnabled('video-demand')) {
            if ($videoDemand['is_video_demand']) {
                return true;
            }
        }

        return false;

    }

    public static function whetherCourse($goods_id)
    {
        $data = CourseGoodsModel::uniacid()->where('goods_id', $goods_id)->value('is_course');

        
        $data = $data === null ? 0 : $data;
        
        return $data;
    }
}