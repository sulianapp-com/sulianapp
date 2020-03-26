<?php

namespace app\frontend\modules\shop\controllers;

use app\api\Base;
use app\common\components\ApiController;
use app\common\facades\Setting;
use app\common\models\Category;
use app\common\models\Goods;
use app\common\models\Slide;
use app\frontend\modules\goods\models\Brand;
use Illuminate\Support\Facades\DB;
use app\common\services\goods\VideoDemandCourseGoods;
use app\common\models\Adv;
use app\common\helpers\Cache;
use Yunshop\Love\Common\Services\SetService;
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/3
 * Time: 22:16
 */
class IndexController extends ApiController
{
    protected $publicAction = ['getDefaultIndex'];

    public function getDefaultIndex()
    {
        $set = Setting::get('shop.category');
        $set['cat_adv_img'] = replace_yunshop(yz_tomedia($set['cat_adv_img']));
        $category = $this->getRecommentCategoryList();
        foreach ($category as &$item) {
            $item['thumb'] = replace_yunshop(yz_tomedia($item['thumb']));
            $item['adv_img'] = replace_yunshop(yz_tomedia($item['adv_img']));
        }
        $data = [
            'ads' => $this->getAds(),
            'category' => $category,
            'set' => $set,
            'goods' => $this->getRecommentGoods(),
        ];
        return $this->successJson('成功', $data);
    }

    //获取推荐品牌
    public function getRecommentBrandList()
    {
        $request = Brand::uniacid()->select('id', 'name', 'logo')->where('is_recommend', 1)->get();
        foreach ($request as &$item) {
            if ($item['logo']) {
                $item['logo'] = replace_yunshop(yz_tomedia($item['logo']));
            }
        }
        return $request;
    }

    //获取限时购商品
    public function getTimeLimitGoods()
    {
        $goods_model = \app\common\modules\shop\ShopConfig::current()->get('goods.models.commodity_classification');
        $goods_model = new $goods_model;
        $time = time();
        $field = ['id', 'thumb', 'title', 'price', 'market_price'];
        $timeGoods = $goods_model->uniacid()->select(DB::raw(implode(',', $field)))
            ->whereHas('hasOneGoodsLimitBuy', function ($query) use ($time) {
                $query->where('status', 1)->where('start_time', '<=', $time);
            })
            ->with('hasOneGoodsLimitBuy')
            ->where("is_recommand", 1)
            ->where("status", 1)
            ->whereInPluginIds()
            ->orderBy("display_order", 'desc')
            ->orderBy("id", 'desc')
            ->get();
        $timeGoods->vip_level_status;
        if (!empty($timeGoods->toArray())) {
            foreach ($timeGoods as $key => &$value) {
                $value->thumb = yz_tomedia($value->thumb);
                $value->hasOneGoodsLimitBuy->start_time = date('Y/m/d H:i:s', $value->hasOneGoodsLimitBuy->start_time);
                $value->hasOneGoodsLimitBuy->end_time = date('Y/m/d H:i:s', $value->hasOneGoodsLimitBuy->end_time);
            }
        }
        return $timeGoods;
    }

    public function getRecommentGoods()
    {
        //$goods = new Goods();
        $field = ['id as goods_id', 'thumb', 'title', 'price', 'market_price'];
//        if (!Cache::has('YZ_Index_goodsList')) {
        $goods_model = \app\common\modules\shop\ShopConfig::current()->get('goods.models.commodity_classification');
        $goods_model = new $goods_model;
        $goodsList = $goods_model->uniacid()->select(DB::raw(implode(',', $field)))
            ->where("is_recommand", 1)
            ->where("status", 1)
            ->whereInPluginIds()
            ->orderBy("display_order", 'desc')
            ->orderBy("id", 'desc')
            ->paginate(16);
//                ->get()->map(function (Goods $goods) {
//                    return $goods->append('vip_price');
//                });

        foreach ($goodsList as $key => &$item){
            $item['vip_level_status'] = $item->vip_level_status;
            $item['thumb'] = yz_tomedia($item->thumb);
//            dd($key,$item->vip_level_status);
        }
           if($goodsList){
              $goodsList = $goodsList->toArray();
           }

//            foreach ($goodsList['data'] as &$value) {
//                $value['thumb'] = yz_tomedia($value['thumb']);
//            }
            if (app('plugins')->isEnabled('love')){
               // $love_basics_set = SetService::getLoveSet();//获取爱心值基础设置
               // $goodsList->love_name = $love_basics_set['name'];
                  foreach ($goodsList['data'] as &$goodsValue){
                      $love_value = \Yunshop\Love\Common\Models\GoodsLove::select('award_proportion')
                          ->where('uniacid',\Yunshop::app()->uniacid)
                          ->where('goods_id',$goodsValue['goods_id'])
                          ->where('award',1)
                          ->first();
                      $goodsValue['award_proportion'] = $love_value->award_proportion;
                  }
            }
//            Cache::put('YZ_Index_goodsList', $goodsList, 4200);

//        } else {
//            $goodsList = Cache::get('YZ_Index_goodsList');
//
//        }
        /*//是否是课程商品
        $videoDemand = new VideoDemandCourseGoods();
        foreach ($goodsList as &$value) {
            $value->thumb = yz_tomedia($value->thumb);
            $value->is_course = $videoDemand->isCourse($value->goods_id);

        }*/
        return $goodsList;
    }

    public function getRecommentCategoryList()
    {

        $request = Category::getRecommentCategoryList()
            ->where('is_home', '1')
            ->pluginId()
            ->get();
        foreach ($request as &$item) {
            $item['thumb'] = replace_yunshop(yz_tomedia($item['thumb']));
            $item['adv_img'] = replace_yunshop(yz_tomedia($item['adv_img']));
        }

        return $request;
    }

    /**
     * @param $goods_id
     * @param null $option_id
     * @return bool|\Illuminate\Database\Eloquent\Model|null|static
     */
    public function getAds()
    {
        $slide = Slide::getSlidesIsEnabled()->get();
        if ($slide) {
            $slide = $slide->toArray();
            foreach ($slide as &$item) {
                $item['thumb'] = replace_yunshop(yz_tomedia($item['thumb']));
            }
        }
        return $slide;
    }

    public function getAdv()
    {
        $adv = Adv::first();
        $advs = [];
        if ($adv) {
            $i = 0;
            foreach ($adv->advs as $key => $value) {
                if ($value['img'] || $value['link']) {
                    $advs[$i]['img'] = yz_tomedia($value['img']);
                    $advs[$i]['link'] = $value['link'];
                    $i += 1;
                }
            }
        }
        return $advs;
    }

    public function getPayProtocol($request, $integrated = null)
    {
        $setting = Setting::get('shop.trade');
        //共享链支付协议开启
        if ($setting['share_chain_pay_open'] == 1) {
            if(is_null($integrated)){
                return $this->successJson('获取成功', htmlspecialchars_decode($setting['pay_content']));
            }else{
                return show_json(1, htmlspecialchars_decode($setting['pay_content']));
            }
            // return $this->successJson('获取成功', str_replace('&nbsp;', '',strip_tags(htmlspecialchars_decode($setting['pay_content']) )) );

        }
        if(is_null($integrated)){
            return $this->errorJson('未开启共享链支付协议');
        }else{
            return show_json(1,'');
        }

    }
}