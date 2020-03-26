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
use app\backend\modules\goods\services\CreateGoodsService;
use app\backend\modules\goods\models\Dispatch;
use app\backend\modules\discount\models\DispatchClassify;
use app\backend\modules\goods\models\GoodsDispatch;
class BatchDispatchController extends BaseController
{

    public function freight(){

        $category = DispatchClassify::uniacid()->get()->toArray();
        foreach ($category as $k => $item) {
            $category[$k]['category_ids'] = Category::select('id', 'name')->whereIn('id', explode(',', $item['dispatch_id']))->get()->toArray();
        }

        return view('discount.freight',[
            'category' => json_encode($category),
        ])->render();
    }


    public function freightSet()
    {
//        $dispatch_templates = Dispatch::select('id','dispatch_name')
//            ->where('uniacid',\YunShop::app()->uniacid)
//            ->where('is_plugin',0)
//            ->get();
//        dd($dispatch_templates);

       // $dispatch = new GoodsDispatch();
        $dispatch_templates = Dispatch::getTemplate();

        return view('discount.freight-set', [
            'list'=>$dispatch_templates,
            'url' => json_encode(yzWebFullUrl('discount.batch-dispatch.freight-save')),
        ])->render();
    }
    public function updateFreight()
    {
        $id=request()->id;
        $form_data=request()->form_data;

        if (!$id){
            throw new ShopException('参数错误!');
        }
        if  ($form_data) {
            $categorys = $form_data['search_categorys'];
            foreach ($categorys as $v){
                $categorys_r[] = $v['id'];
            }
            $category_ids = implode(',', $categorys_r);
            $data = [
                'uniacid' => \YunShop::app()->uniacid,
                'dispatch_id' => $category_ids,
                'freight_type' => $form_data['freight_type'],
                'freight_value' => $form_data['freight_value'],
                'template_id' => $form_data['template_id'],
                'is_cod'=>$form_data['is_cod'],
            ];
                if(!(DispatchClassify::find($id)->update($data))){
                    return $this->errorJson("修改失敗");
                }
            foreach( $categorys_r as  $categoryID){
                $this->updateGoodsDispatch($data,$categoryID);
            }
               // $this->updateGoodsDispatch($data);
            return $this->successJson('ok');

        }
        $categoryDiscount = DispatchClassify::find($id);
        $categoryDiscount['category_ids'] = Category::select('id', 'name')
            ->whereIn('id', explode(',', $categoryDiscount['dispatch_id']))
            ->get()->toArray();
        $dispatch_templates = Dispatch::getTemplate();

        return view('discount.freight-set', [
            'list'=>$dispatch_templates,
            'categoryDispach' => json_encode($categoryDiscount),
            'url' => json_encode(yzWebFullUrl('discount.batch-dispatch.update-freight',['id' => $id])),
        ])->render();
    }

    public function freightSave(){
           $form_data = request()->form_data;
           $pay = Setting::get('shop.pay')['COD'];
            if ($form_data) {
                $categorys = $form_data['search_categorys'];
                foreach ($categorys as $v) {
                    $categorys_r[] = $v['id'];
                }
                $category_ids = implode(',', $categorys_r);
                $data = [
                    'uniacid' => \YunShop::app()->uniacid,
                    'dispatch_id' => $category_ids,
                    'freight_type' => $form_data['freight_type'],
                    'freight_value' => $form_data['freight_value'],
                    'template_id' => $form_data['template_id'],
                    'is_cod'=>$pay,
                ];
                $model = new DispatchClassify();
                $model->fill($data);
                if ($model->save()) {
                    foreach( $categorys_r as  $categoryID){
                        $this->updateGoodsDispatch($data,$categoryID);
                    }
                    return $this->successJson('ok');

                }
            }

        return view('discount.freight-set', [
            'url' => json_encode(yzWebFullUrl('discount.batch-dispatch.freight')),
        ])->render();
    }

    public function updateGoodsDispatch($data,$categoryID){
        //2级联动
       if ( Setting::get('shop.category')['cat_level']==2){
           //$goods_ids = GoodsCategory::select('goods_id')
           $goods_ids = GoodsCategory::select('goods_id')
               ->whereHas('goods', function ($query) {
                   $query->where('is_plugin',0)->where('plugin_id',0);
               })
               ->where('category_ids','like', '%,'.$categoryID.',%')
               ->get()
               ->toArray();

           $goods_id = GoodsCategory::select('goods_id')
               ->whereHas('goods', function ($query) {
                   $query->where('is_plugin',0)->where('plugin_id',0);
               })
               ->where('category_id', $categoryID)
               ->get()
               ->toArray();

           $arr = array_merge($goods_ids, $goods_id);
       }else {
           $arr = GoodsCategory::select('goods_id')
               ->whereHas('goods', function ($query) {
                   $query->where('is_plugin', 0)->where('plugin_id', 0);
               })
               ->where('category_id', $categoryID)
               ->get()
               ->toArray();

       }

        foreach ($arr as $goods_id) {
            $item_id[] = $goods_id['goods_id'];
        }

        foreach($item_id as $goodsID){
            GoodsDispatch::freightSave($goodsID,$data);
        }
    }


    public function selectCategory()
    {
        $kwd = \YunShop::request()->keyword;
        if ($kwd) {
            $category = Category::getMallCategorysByName($kwd);
            return $this->successJson('ok', $category);
        }
    }

    public function deleteSet()
    {
        if (CategoryDiscount::find(request()->id)->delete()) {
            return $this->successJson('ok');
        };
    }

    public function deleteFreigh(){
        if (DispatchClassify::find(request()->id)->delete()){
            return $this->successJson("ok");
        }
    }

}