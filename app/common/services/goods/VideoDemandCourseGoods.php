<?php
/**
 *  Create date 2018/1/3 14:24
 *  Author: 芸众商城 www.yunzshop.com
 */

namespace app\common\services\goods;

use app\common\components\BaseController;
use Setting;

class VideoDemandCourseGoods extends BaseController
{   
    //视频点播插件设置
    protected $videoDemand;

    public function __construct()
    {
        parent::__construct();

        $this->videoDemand = Setting::get('plugin.video_demand');
    }


    /**
     * 是否启用视频点播
     */
    public function whetherEnabled()
    {
        if (app('plugins')->isEnabled('video-demand')) {
            if ($this->videoDemand['is_video_demand']) {
                return true;
            }
        }

        return false;
    }

    /**
     * 是课程商品的id集合
     * @return [array] [$courseGoods_ids]
     */
    public function courseGoodsIds()
    {
        //是否启用视频点播
        $courseGoods_ids = [];

        if ($this->whetherEnabled()) {
            
            $courseGoods = \Yunshop\VideoDemand\models\CourseGoodsModel::getCourseGoodsIdsData()->toArray();
            foreach ($courseGoods as $value) {
                $courseGoods_ids[] = $value['goods_id'];
            }
        }

        return $courseGoods_ids;
    }

    /**
     * 商品是否是课程
     * @param  [int]  $goods_id [商品id]
     * @return int    $data 0 不是|1 是
     */
    public function isCourse($goods_id)
    {

        if ($this->whetherEnabled()) {
            
            $data = \Yunshop\VideoDemand\models\CourseGoodsModel::uniacid()
                                    ->select('is_course')
                                    ->where('goods_id', $goods_id)
                                    ->value('is_course');
        }

        $data = $data === null ? 0 : $data;

        return $data;
    }

}