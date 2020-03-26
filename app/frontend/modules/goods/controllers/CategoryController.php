<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/3
 * Time: 下午2:29
 */

namespace app\frontend\modules\goods\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\models\Slide;
use Illuminate\Support\Facades\Cookie;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Session\Store;
use app\frontend\modules\goods\models\Category;
use app\frontend\modules\goods\services\CategoryService;
use app\common\models\Goods;
use app\common\models\GoodsSpecItem;
use Yunshop\Designer\models\ViewSet;

class CategoryController extends BaseController
{
    public function getCategory()
    {
        $pageSize = 100;
        $parent_id = \YunShop::request()->parent_id ?: '0';
        $list = Category::getCategorys($parent_id)->pluginId()->where('enabled', 1)->paginate($pageSize)->toArray();

        if (!$list['data']) {
            return $this->errorJson('未检测到分类数据!');
        }

        foreach ($list['data'] as &$item) {
            $item['thumb'] = replace_yunshop(yz_tomedia($item['thumb']));
            $item['adv_img'] = replace_yunshop(yz_tomedia($item['adv_img']));
        }

        return $this->successJson('获取分类数据成功!', $list);
    }

    public function categoryHome()
    {
        $res = app('plugins')->isEnabled('designer');
        $category_data = [
            'names'     => '02',
            'type'      => 'category',
        ];
        $category_template = $category_data;
        if ($res){
            $category_template = ViewSet::uniacid()->where('type','category')->select('names','type')->first();
            $category_template = $category_template ?: $category_data;
        }
        $set = \Setting::get('shop.category');
        $pageSize = 100;
        $parent_id = \YunShop::request()->parent_id ?: '0';
        $list = Category::getCategorys($parent_id)->pluginId()->where('enabled', 1)->paginate($pageSize)->toArray();

        if (!$list['data']) {
            return $this->errorJson('未检测到分类数据!');
        }

        foreach ($list['data'] as &$item) {
            $item['thumb'] = replace_yunshop(yz_tomedia($item['thumb']));
            $item['adv_img'] = replace_yunshop(yz_tomedia($item['adv_img']));
        }

        $set['cat_adv_img'] = replace_yunshop(yz_tomedia($set['cat_adv_img']));

        $recommend = $this->getRecommendCategoryList();
        // 获取推荐分类的第一个分类下的商品返回
        if (!empty($recommend)) {
            $goods_list = $this->getGoodsList($recommend[0]['id'],1);
        } else {
            $goods_list = [];
        }

        return $this->successJson('获取分类数据成功!', [
            'category' => $list,
            'recommend' => $recommend,
            'member_cart' => $this->getMemberCart(),
            'goods_list' => $goods_list,
            'ads' => $this->getAds(),
            'set' => $set,
            'category_template' => $category_template
        ]);
    }
    protected function getMemberCart()
    {
        // 会员未登录，购物车没数据的
        try{
            $uid = \app\frontend\models\Member::current()->uid;
        } catch (\app\common\exceptions\MemberNotLoginException $e) {
            return [];
        }

        $cartList = app('OrderManager')->make('MemberCart')->carts()->where('member_id', $uid)
            ->pluginId()
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
        foreach ($cartList as $key => $cart) {
            $cartList[$key]['option_str'] = '';
            $cartList[$key]['goods']['thumb'] = yz_tomedia($cart['goods']['thumb']);
            if (!empty($cart['goods_option'])) {
                //规格数据替换商品数据
                if ($cart['goods_option']['title']) {
                    $cartList[$key]['option_str'] = $cart['goods_option']['title'];
                }
                if ($cart['goods_option']['thumb']) {
                    $cartList[$key]['goods']['thumb'] = yz_tomedia($cart['goods_option']['thumb']);
                }
                if ($cart['goods_option']['market_price']) {
                    $cartList[$key]['goods']['price'] = $cart['goods_option']['product_price'];
                }
                if ($cart['goods_option']['market_price']) {
                    $cartList[$key]['goods']['market_price'] = $cart['goods_option']['market_price'];
                }
            }
        }
        return $cartList;
    }

    public function getAds()
    {
        $slide = Slide::getSlidesIsEnabled()->get();
        if (!$slide->isEmpty()) {
            $slide = $slide->toArray();
            foreach ($slide as &$item) {
                $item['thumb'] = replace_yunshop(yz_tomedia($item['thumb']));
            }
        }
        return $slide;
    }

    /*
     * 通过某个分类id获取分类下的商品
     */
    public function getGoodsListByCategoryId()
    {
        $category_id = \YunShop::request()->category_id;
        if (empty($category_id)) {
            return $this->errorJson("分类不能为空",[]);
        }
        $goods_page = \YunShop::request()->goods_page ?: 1;
        return $this->successJson('获取商品成功!', $this->getGoodsList($category_id,$goods_page));
    }

    /**
     * 获取分类下的商品和规格
     */
    public function getGoodsList($category_id,$goods_page)
    {
        $goods_model = \app\common\modules\shop\ShopConfig::current()->get('goods.models.commodity_classification');
        $goods_model = new $goods_model;
        $list = $goods_model->uniacid()->select(['yz_goods.id','yz_goods.title','yz_goods.thumb','yz_goods.market_price','yz_goods.price','yz_goods.cost_price','yz_goods.stock','yz_goods.real_sales','yz_goods.show_sales','yz_goods.virtual_sales','yz_goods.has_option'])
            ->with(['hasManySpecs' => function ($query) {
            return $query->select('id', 'goods_id', 'title', 'description')->with(['hasManySpecsItem'=>function($query){
                return $query->select('id', 'title', 'specid', 'thumb');
            }]);
        }, 'hasManyOptions' => function ($query) {
                return $query->select('id', 'goods_id', 'title', 'thumb', 'product_price', 'market_price', 'stock', 'specs', 'weight');
            }])
            //->search(['category'=>$category_id])
            ->join('yz_goods_category', function ($join) use ($category_id) {
                $join->on('yz_goods_category.goods_id', '=', 'yz_goods.id')
                    ->whereRaw('FIND_IN_SET(?,category_ids)', [$category_id]);
            })
            // 由于一个商品可选多种分类，会出现查询商品重复的情况，需要对商品id分组达到去重效果
            ->groupBy('yz_goods.id')
            ->where('yz_goods.status',1)->orderBy('yz_goods.display_order', 'desc')->orderBy('yz_goods.id', 'desc')
            ->paginate(20,['*'],'page',$goods_page);
        $list->vip_level_status;
        foreach ($list as $goodsModel) {
            //前端需要goods_id
            $goodsModel->goods_id = $goodsModel->id;
            $goodsModel->buyNum = 0;
            if (strexists($goodsModel->thumb, 'image/')) {
                $goodsModel->thumb = yz_tomedia($goodsModel->thumb,'image');
            } else {
                $goodsModel->thumb = yz_tomedia($goodsModel->thumb);
            }

            foreach ($goodsModel->hasManySpecs as &$spec) {
                foreach ($spec->hasManySpecsItem as &$specitem) {
                    $specitem->thumb = yz_tomedia($specitem->thumb);
                }
            }

            if ($goodsModel->hasManyOptions && $goodsModel->hasManyOptions->toArray()) {
                foreach ($goodsModel->hasManyOptions as &$item) {
                    $item->thumb = replace_yunshop(yz_tomedia($item->thumb));
                }
            }
            if ($goodsModel->has_option) {
                $goodsModel->min_price = $goodsModel->hasManyOptions->min("product_price");
                $goodsModel->max_price = $goodsModel->hasManyOptions->max("product_price");
                $goodsModel->stock = $goodsModel->hasManyOptions->sum('stock');
            }
        }
        return  $list->toArray();
    }

    /**
     * 获取推荐分类
     * @return mixed
     */
    public function getRecommendCategoryList()
    {
        $request = Category::getRecommentCategoryList()
            ->where(['is_home'=> '1','enabled' => '1'])
            ->pluginId()
            ->get()
            ->toArray();
        foreach ($request as &$item) {
            $item['thumb'] = replace_yunshop(yz_tomedia($item['thumb']));
            $item['adv_img'] = replace_yunshop(yz_tomedia($item['adv_img']));
        }

        return $request;
    }
    
    public function getChildrenCategory()
    {
        $pageSize = 100;
        $set = \Setting::get('shop.category');
        $parent_id = intval(\YunShop::request()->parent_id);
        $list = Category::getChildrenCategorys($parent_id,$set)->where('enabled',1)->paginate($pageSize)->toArray();
        foreach ($list['data'] as &$item) {
            $item['thumb'] = replace_yunshop(yz_tomedia($item['thumb']));
            $item['adv_img'] = replace_yunshop(yz_tomedia($item['adv_img']));
            foreach ($item['has_many_children'] as &$has_many_child) {
                $has_many_child['thumb'] = replace_yunshop(yz_tomedia($has_many_child['thumb']));
                $has_many_child['adv_img'] = replace_yunshop(yz_tomedia($has_many_child['adv_img']));
            }
        }

        // 增加分类下的商品返回。
        // 逻辑为：点击一级分类，如果三级分类未开启，则将一级分类下的第一个二级分类的商品返回
        // 如果开启三级分类，则取三级分类的第一个分类下的商品返回
        if (!empty($list['data'])) {
            if (empty($list['data'][0]['has_many_children'])) {
                $list['goods_list'] = $this->getGoodsList($list['data'][0]['id'],1);
            } else {
                $list['goods_list'] = $this->getGoodsList($list['data'][0]['has_many_children'][0]['id'],1);
            }
        } else {
            $list['goods_list'] = [];
        }
        $set['cat_adv_img'] = replace_yunshop(yz_tomedia($set['cat_adv_img']));
        $list['set'] = $set;

        // 默认返回等级2
        if (empty($list['set']['cat_level'])){
            $list['set']['cat_level'] = 2;
        }
        
        if($list){
            return $this->successJson('获取子分类数据成功!', $list);
        }
        return $this->errorJson('未检测到子分类数据!',$list);
    }

    public function searchGoodsCategory()
    {
        $set = \Setting::get('shop.category');
        $json_data = [];
        $list = Category::getCategorys(0)->pluginId()->where('enabled', 1)->get()->toArray();
        foreach ($list as &$parent) {
            $parent['son'] = Category::getChildrenCategorys($parent['id'],$set)->get()->toArray();
            foreach ($parent['son'] as &$value) {
                $value['thumb'] = replace_yunshop(yz_tomedia($value['thumb']));
                $value['adv_img'] = replace_yunshop(yz_tomedia($value['adv_img']));
                if (!is_null($value['has_many_children'])) {
                    foreach ($value['has_many_children'] as &$has_many_child) {
                        $has_many_child['thumb'] = replace_yunshop(yz_tomedia($has_many_child['thumb']));
                        $has_many_child['adv_img'] = replace_yunshop(yz_tomedia($has_many_child['adv_img']));
                    }
                } else {
                    $value['has_many_children'] = [];
                }
            }
            $parent['thumb'] = replace_yunshop(yz_tomedia($parent['thumb']));
            $parent['adv_img'] = replace_yunshop(yz_tomedia($parent['adv_img']));
        }

        return $this->successJson('获取子分类数据成功!', $list);
    }

//    public function getCategorySetting()
//    {
//        $set = Setting::get('shop.category');
//        if($set){
//            return $this->successJson('获取分类设置数据成功!', $set);
//        }
//        return $this->errorJson('未检测到分类设置数据!',$set);
//    }
    /**
     * 商城快速选购展示分类
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function fastCategory(){

        $list = Category::select('id', 'name', 'thumb', 'adv_img', 'adv_url')->uniacid()->where('level',1)->where('parent_id',0)->get();
        $list->map(function($category){
            $category->childrens = Category::select('id', 'name', 'thumb', 'adv_img', 'adv_url')->where('level',2)->where('parent_id',$category->id)->get();
        });

        if($list->isEmpty()){
            throw new AppException('未检测到分类数据');
        }
        return $this->successJson('获取分类成功!',['list'=>$list->toArray()]);
    }
}