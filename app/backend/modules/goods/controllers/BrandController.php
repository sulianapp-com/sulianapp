<?php
namespace app\backend\modules\goods\controllers;

use app\backend\modules\goods\models\Brand;
use app\backend\modules\goods\services\BrandService;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/27
 * Time: 上午9:17
 */
class BrandController extends BaseController
{
    /**
     * 商品品牌列表
     */
    public function index()
    {
        $pageSize = 20;
        $list = Brand::getBrands()->paginate($pageSize)->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);
        
        return view('goods.brand.list', [
            'list' => $list,
            'pager' => $pager,
        ])->render();
    }

    /**
     * 添加品牌
     */
    public function add()
    {
        $brandModel = new Brand();

        $requestBrand = \YunShop::request()->brand;

        if($requestBrand) {
            //将数据赋值到model
            $brandModel->setRawAttributes($requestBrand);
            //其他字段赋值
            $brandModel->uniacid = \YunShop::app()->uniacid;

            //字段检测
            $validator = $brandModel->validator($brandModel->getAttributes());
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {
                //数据保存
                if ($brandModel->save()) {
                    //显示信息并跳转
                    return $this->message('品牌创建成功', Url::absoluteWeb('goods.brand.index'));
                }else{
                    $this->error('品牌创建失败');
                }
            }
        }

        $this->title = '创建品牌';
        $this->breadcrumbs = [
            '品牌管理'=>['url'=>$this->createWebUrl('goods.brand.index'),'icon'=>'icon-dian'],
            $this->title,
        ];

        return view('goods.brand.info', [
            'brandModel' => $brandModel
        ])->render();
    }


    /**
     * 编辑商品品牌
     */
    public function edit()
    {

        $brandModel = Brand::getBrand(\YunShop::request()->id);
        if(!$brandModel){
            return $this->message('无此记录或已被删除','','error');
        }
        $requestBrand = \YunShop::request()->brand;
        if($requestBrand) {
            //将数据赋值到model
            $brandModel->setRawAttributes($requestBrand);
            //字段检测
            $validator = $brandModel->validator($brandModel->getAttributes());
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {
                //数据保存
                if ($brandModel->save()) {
                    //显示信息并跳转
                    return $this->message('品牌保存成功', Url::absoluteWeb('goods.brand.index'));
                }else{
                    $this->error('品牌保存失败');
                }
            }
        }
        return view('goods.brand.info', [
            'brandModel' => $brandModel
        ])->render();
    }

    /**
     * 删除商品品牌
     */
    public function deletedBrand()
    {
        $brand = Brand::getBrand(\YunShop::request()->id);
        if(!$brand) {
            return $this->message('无此品牌或已经删除','','error');
        }

        $result = Brand::deletedBrand(\YunShop::request()->id);
        if($result) {
           return $this->message('删除品牌成功',Url::absoluteWeb('goods.brand.index'));
        }else{
            return $this->message('删除品牌失败','','error');
        }
    }

}