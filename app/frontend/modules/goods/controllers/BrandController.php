<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/3
 * Time: 下午2:29
 */

namespace app\frontend\modules\goods\controllers;

use app\common\components\ApiController;
use Illuminate\Support\Facades\Cookie;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Session\Store;
use app\common\models\Goods;
use app\frontend\modules\goods\models\Brand;
use app\frontend\modules\goods\services\BrandService;

class BrandController extends ApiController
{
    public function getBrand()
    {
        $pageSize = 100;
        $list = Brand::getBrands()->paginate($pageSize)->toArray();
        if($list['data']){
            foreach ($list['data'] as &$item) {
                $item['logo'] = replace_yunshop(yz_tomedia($item['logo']));
            }
            return $this->successJson('获取品牌数据成功!', $list);
        }
        return $this->errorJson('未检测到品牌数据!', $list);
    }

    public function getBrandGoods()
    {
        $id = intval(\YunShop::request()->id);
        if (!$id) {
            return $this->errorJson('请传入正确参数.');
        }
        $brand_detail = Brand::uniacid()->select("name", "logo", "id", "desc")->find($id);

        if (!$brand_detail) {
            return $this->errorJson('品牌已被删除或不存在...');
        }

        if ($brand_detail->logo) {
            $brand_detail->logo = yz_tomedia($brand_detail->logo);
        }
        $brand_detail->desc = html_entity_decode($brand_detail->desc);

        $list = Goods::select('id', 'id as goods_id', 'title', 'thumb', 'price', 'market_price')
            ->where("status", 1)
            ->where(function($query) {
                $query->where("plugin_id", 0)->orWhere('plugin_id', 40)->orWhere('plugin_id', 92);
            })->where('brand_id', $id)->orderBy('display_order', 'desc')
            ->paginate(20)->toArray();

        if ($list['total'] > 0) {
            $data = collect($list['data'])->map(function($rows) {
                return collect($rows)->map(function($item, $key) {
                    if ($key == 'thumb' && preg_match('/^images/', $item)) {
                        return replace_yunshop(yz_tomedia($item));
                    } else {
                        return $item;
                    }
                });
            })->toArray();

            $list['data'] = $data;
        }

        if (empty($list)) {
            return $this->errorJson('该品牌下没有商品.');
        }
        $brand_detail['goods'] = $list; 

        return $this->successJson('成功', $brand_detail);
    }
    public function getBrandDetail()
    {
        $id = intval(\YunShop::request()->id);
        if (!$id) {
            return $this->errorJson('请传入正确参数.');
        }
        $brand_detail = Brand::uniacid()->select("name", "logo", "id", "desc")->find($id);

        if (!$brand_detail) {
            return $this->errorJson('品牌已被删除或不存在...');
        }

        if ($brand_detail->logo) {
            $brand_detail->logo = yz_tomedia($brand_detail->logo);
        }
        $brand_detail->desc = html_entity_decode($brand_detail->desc);

        return $this->successJson('brand_detail', $brand_detail);
    }
}