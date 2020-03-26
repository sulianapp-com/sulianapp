<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/2
 * Time: 上午11:51
 */

namespace app\backend\modules\goods\services;

use app\backend\modules\goods\models\Goods;
use app\backend\modules\goods\models\GoodsSpecItem;
use app\backend\modules\goods\models\GoodsParam;
use app\backend\modules\goods\models\GoodsSpec;
use app\backend\modules\goods\models\GoodsOption;
use app\backend\modules\goods\models\Brand;
use app\common\models\GoodsCategory;
use Setting;

class EditGoodsService
{
    public $goods_id;
    public $goods_model;
    public $request;
    public $catetory_menus;
    public $brands;
    public $optionsHtml;
    public $type;

    public function __construct($goods_id, $request, $type = 0)
    {
        $this->type = $type;
        $this->goods_id = $goods_id;
        $this->request = $request;
        $this->goods_model = Goods::with('hasOneGoodsVideo')->with(['hasManyParams' => function ($query) {
            return $query->orderBy('displayorder', 'asc');
        }])->with(['hasManySpecs' => function ($query) {
            return $query->orderBy('display_order', 'asc');
        }])->with('hasManyGoodsCategory')->find($goods_id);
    }

    public function edit()
    {
        //商品属性默认值
        $arrt_default = [
            'is_recommand' => 0,
            'is_new' => 0,
            'is_hot' => 0,
            'is_discount' => 0
        ];

        //获取规格名及规格项
        $goods_data = $this->request->goods;

        $goods_data = array_merge($arrt_default, $goods_data);

        foreach ($this->goods_model->hasManySpecs as &$spec) {
            $spec['items'] = GoodsSpecItem::where('specid', $spec['id'])->orderBy('display_order', 'asc')->get()->toArray();
        }

        //获取具体规格内容html
        $this->optionsHtml = GoodsOptionService::getOptions($this->goods_id, $this->goods_model->hasManySpecs);

        //商品其它图片反序列化
        $this->goods_model->thumb_url = !empty($this->goods_model->thumb_url) ? unserialize($this->goods_model->thumb_url) : [];

        if ($goods_data) {

            // 正则匹配富文本更改视频标签样式
            //$goods_data['content'] = preg_replace(htmlspecialchars('/<p[^>]*/'), htmlspecialchars('<p style="display: inline-block;"'), $goods_data['content']);
            $goods_data['content'] = preg_replace('/class="[^=]*/', 'class="edui-upload-video" controls', htmlspecialchars_decode($goods_data['content']));

            preg_match('/<video[^>]*/',  $goods_data['content'], $matches);

            $matches[0] .= ' x5-playsinline="true" webkit-playsinline="true" playsinline="true"';

            $goods_data['content'] = preg_replace('/<video[^>]*/', $matches[0], $goods_data['content']);
            $goods_data['content'] = htmlspecialchars($goods_data['content']);

            if ($this->type == 1) {
                $goods_data['status'] = 0;
            }
            if (!$goods_data['virtual_sales']) {
                $goods_data['virtual_sales'] = 0;
            }
            $goods_data['has_option'] = $goods_data['has_option'] ? $goods_data['has_option'] : 0;
            $goods_data['weight'] = $goods_data['weight'] ? $goods_data['weight'] : 0;

            if(isset($goods_data['thumb_url'])){
                $goods_data['thumb_url'] = serialize($goods_data['thumb_url']);
            } else {
                $goods_data['thumb_url'] = '';
            }



            $category_model = GoodsCategory::where("goods_id", $this->goods_model->id)->first();
            if (!empty($category_model)) {
                $category_model->delete();
            }
            GoodsService::saveGoodsMultiCategory($this->goods_model, \YunShop::request()->category, Setting::get('shop.category'));
/*
            if (!empty($this->request->widgets['sale']['max_point_deduct'])
                && !empty($goods_data['price'])
                && $this->request->widgets['sale']['max_point_deduct'] > $goods_data['price']) {
                return ['status' => -1, 'msg' => '积分抵扣金额大于商品现价'];
            }
*/
            $goods_data['price'] = $goods_data['price'] ?: 0;
            $goods_data['market_price'] = $goods_data['market_price'] ?: 0;
            $goods_data['cost_price'] = $goods_data['cost_price'] ?: 0;

            $this->goods_model->setRawAttributes($goods_data);
            $this->goods_model->widgets = $this->request->widgets;
            //其他字段赋值
            $this->goods_model->uniacid = \YunShop::app()->uniacid;
            $this->goods_model->id = $this->goods_id;
            //数据保存
            $validator = $this->goods_model->validator($this->goods_model->getAttributes());
            if ($validator->fails()) {
                return ['status' => -1, 'msg' => $validator->messages()];
                //$this->error($validator->messages());
            } else {
                if ($this->goods_model->save()) {
                    GoodsParam::saveParam($this->request, $this->goods_model->id, \YunShop::app()->uniacid);
                    GoodsSpec::saveSpec($this->request, $this->goods_model->id, \YunShop::app()->uniacid);
                    GoodsOption::saveOption($this->request, $this->goods_model->id, GoodsSpec::$spec_items, \YunShop::app()->uniacid);
                    //显示信息并跳转
                    return ['status' => 1];
                } else {
                    return ['status' => -1];
                }
            }

        }

        $this->brands = Brand::getBrands()->get();

        if (isset($this->goods_model->hasManyGoodsCategory[0])){
            foreach($goods_categorys = $this->goods_model->hasManyGoodsCategory->toArray() as $goods_category){
                $this->catetory_menus[] = CategoryService::getCategoryMultiMenu(['catlevel' => Setting::get('shop.category')['cat_level'], 'ids' => explode(",", $goods_category['category_ids'])]);
            }

        } else {
            $this->catetory_menus[] = CategoryService::getCategoryMultiMenu(['catlevel' => Setting::get('shop.category')['cat_level'], 'ids' => []]);
        }

    }
}