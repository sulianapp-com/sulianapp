<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/23 下午2:26
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\link\controllers;


use app\common\components\BaseController;
use app\backend\modules\goods\models\Categorys;

class LinkController extends BaseController
{


    public function categoryLink()
    {

        $category_data = Categorys::getCategory();

        return json_encode($category_data);
    }



    public function brandLink(){
        $brands = \app\common\models\Brand::getBrands()->select('id', 'name')->get()->toArray();
        $brand_data = [];

        foreach ($brands as $key => $brand){
            $brand_data[$key] = $brand;
            $brand_data[$key]['url'] =  yzAppFullUrl('brandgoods/'.$brand['id']);

        }
        return json_encode($brand_data);
    }

    public function smallProceduresBrandLink(){
        $brands = \app\common\models\Brand::getBrands()->select('id', 'name')->get()->toArray();
        $brand_data = [];

        foreach ($brands as $key => $brand){
            $brand_data[$key] = $brand;
            $brand_data[$key]['url'] =  '/packageB/member/category/brandgoods/brandgoods?id='.$brand['id'];

        }
        
        return json_encode($brand_data);
    }



}
