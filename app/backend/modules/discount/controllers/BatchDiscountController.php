<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/14
 * Time: 15:28
 */

namespace app\backend\modules\discount\controllers;


use app\backend\modules\discount\models\CategoryDiscount;
use app\backend\modules\goods\models\Category;
use app\backend\modules\goods\models\Discount;
use app\backend\modules\member\models\MemberLevel;
use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\facades\Setting;
use app\common\models\GoodsCategory;
use Illuminate\Support\Facades\DB;


class BatchDiscountController extends BaseController
{
    public function index()
    {
        $category = CategoryDiscount::uniacid()->get()->toArray();

        foreach ($category as $k => $item) {
            $category[$k]['category_ids'] = Category::select('id', 'name')->whereIn('id', explode(',', $item['category_ids']))->get()->toArray();
        }

        return view('discount.discount',[
            'category' => json_encode($category),
        ])->render();
    }

    public function updateSet()
    {
        $form_data = request()->form_data;
        $id = request()->id;
        if (!$id) {
            throw new ShopException('参数错误!');
        }

        if ($form_data) {
            $form_data['discount_value'] = array_filter($form_data['discount_value']);
            $discount = $form_data['discount_value'];
            $categorys = $form_data['search_categorys'];
            foreach ($categorys as $v){
                $categorys_r[] = $v['id'];
            }
            $categorys_r_unique = array_unique($categorys_r);
            $categoryModel = CategoryDiscount::find($id);
            $category_ids = implode(',', $categorys_r_unique);

            $data = [
                'category_ids' => $category_ids,
                'uniacid' => \YunShop::app()->uniacid,
                'level_discount_type' => $form_data['discount_type'],
                'discount_method' => $form_data['discount_method'],
                'discount_value' => $discount,
                'created_at' => time(),
            ];

            $categoryModel->fill($data);
            if ($categoryModel->save()) {
                $goods_ids = GoodsCategory::select('goods_id')->whereIn('category_id', explode(',', $data['category_ids']))->get()->toArray();
                foreach ($goods_ids as $goods_id) {
                    $item_id[] = $goods_id['goods_id'];
                }
                foreach ($item_id as $goodsId) {
                    Discount::relationSave($goodsId, $data);
                }
                return $this->successJson('ok');
            }
        }

        $levels = MemberLevel::getMemberLevelList();
        $levels = array_merge($this->defaultLevel(), $levels);

        $categoryDiscount = CategoryDiscount::find($id);
        $categoryDiscount['category_ids'] = Category::select('id', 'name')
            ->whereIn('id', explode(',', $categoryDiscount['category_ids']))
            ->get()->toArray();

        return view('discount.set', [
            'levels' => json_encode($levels),
            'categoryDiscount' => json_encode($categoryDiscount),
            'url' => json_encode(yzWebFullUrl('discount.batch-discount.update-set',['id' => $id])),
        ])->render();
    }

    public function allSet()
    {
        $set_data = request()->form_data;

        if ($set_data) {
            Setting::set('discount.all_set', $set_data);
            return $this->successJson('ok');
        }
        $set = Setting::get('discount.all_set');

        return view('discount.all-set',[
            'set' => json_encode($set),
        ])->render();
    }

    public function store()
    {
        $form_data = request()->form_data;

        if ($form_data) {
            $form_data['discount_value'] = array_filter($form_data['discount_value']);
            $discount = $form_data['discount_value'];

            $categorys = $form_data['search_categorys'];
            foreach ($categorys as $v){
                $categorys_r[] = $v['id'];
            }
            $category_ids = implode(',', $categorys_r);

            $data = [
                'category_ids' => $category_ids,
                'uniacid' => \YunShop::app()->uniacid,
                'level_discount_type' => $form_data['discount_type'],
                'discount_method' => $form_data['discount_method'],
                'discount_value' => $discount,
                'created_at' => time(),
            ];
            $model = new CategoryDiscount();
            $model->fill($data);
            if ($model->save()) {
                $goods_ids = GoodsCategory::select('goods_id')->whereIn('category_id', explode(',', $data['category_ids']))->get()->toArray();
                foreach ($goods_ids as $goods_id) {
                    $item_id[] = $goods_id['goods_id'];
                }
                foreach ($item_id as $goodsId) {
                    Discount::relationSave($goodsId, $data);
                }
                return $this->successJson('ok');
            }
        }

        $levels = MemberLevel::getMemberLevelList();
        $levels = array_merge($this->defaultLevel(), $levels);

        return view('discount.set', [
            'levels' => json_encode($levels),
            'url' => json_encode(yzWebFullUrl('discount.batch-discount.store')),
        ])->render();
    }

    public function selectCategory()
    {
        $kwd = \YunShop::request()->keyword;
        if ($kwd) {
            $category = Category::getNotOneCategorysByName($kwd);
            return $this->successJson('ok', $category);
        }
    }

    public function deleteSet()
    {
        if (CategoryDiscount::find(request()->id)->delete()) {
            return $this->successJson('ok');
        };
    }

    private function defaultLevel()
    {
        return [
            '0'=> [
                'id' => "0",
                'level' => "0",
                'level_name' => \Setting::get('shop.member.level_name') ?: '普通会员'
            ],
        ];
    }
}