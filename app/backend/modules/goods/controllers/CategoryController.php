<?php
namespace app\backend\modules\goods\controllers;

use app\backend\modules\goods\models\Category;
use app\backend\modules\goods\models\Goods;

use app\backend\modules\goods\services\CategoryService;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\models\GoodsCategory;
use Illuminate\Support\Facades\Input;
use app\common\helpers\Url;
use Setting;
use Illuminate\Support\Facades\DB;
use app\backend\modules\filtering\models\Filtering;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/22
 * Time: 下午1:51
 */

class CategoryController extends BaseController
{
    /**
     * 商品分类列表
     */
    public function index()
    {
        $shopset   = Setting::get('shop');
        //页数
        $pageSize = 10;
        $parent_id = \YunShop::request()->parent_id ? \YunShop::request()->parent_id : '0';
        //查询父分类
        $parent = Category::getCategory($parent_id);
        //分类模型Category
        $list = Category::getCategorys($parent_id)->pluginId()->paginate($pageSize)->toArray();
//        dd($list);
        //分页按钮(不确定)
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);
        return view('goods.category.list', [
            'list' => $list['data'],
            'parent' => $parent,
            'pager' => $pager,
            'shopset' => $shopset
        ])->render();
    }

    /**
     * 添加商品分类
     */
    public function addCategory()
    {

        // sleep(5);
        //判断分类等级
        $level = \YunShop::request()->level ? \YunShop::request()->level : '1';
        //判断是否有父类id没有默认0
        $parent_id = \YunShop::request()->parent_id ? \YunShop::request()->parent_id : '0';

        $categoryModel = new Category();
        //分类等级
        $categoryModel->level = $level;
        //父类id
        $categoryModel->parent_id = $parent_id;
        $parent = [];
        //url地址
        $url = Url::absoluteWeb('goods.category.index');
        if($parent_id > 0) {
            //查出父分类
            $parent = Category::getCategory($parent_id);
            //地址栏显示父分类
            $url = Url::absoluteWeb('goods.category.index',['parent_id'=>$parent_id]);
        }
        //获取分类发送过来的值
        $requestCategory = \YunShop::request()->category;
        if ($requestCategory) {
            if (isset($requestCategory['filter_ids']) && is_array($requestCategory['filter_ids'])) {
                $requestCategory['filter_ids'] = implode(',', $requestCategory['filter_ids']);
            }
            //将数据赋值到model
            $categoryModel->fill($requestCategory);
            //其他字段赋值
            $categoryModel->uniacid = \YunShop::app()->uniacid;
            //字段检测
            $validator = $categoryModel->validator();
            if ($validator->fails()) {
                //检测失败
                $this->error($validator->messages());
            } else {
                //数据保存
                if ($categoryModel->save()) {
                    //显示信息并跳转
                    return $this->message('分类创建成功', $url);
                }else{
                    $this->error('分类创建失败');
                }
            }
        }
        return view('goods.category.info', [
            'item' => $categoryModel,
            'parent' => $parent,
            'level' => $level,
            'label_group' => [],
        ])->render();
    }
    
    /**
     * 修改分类
     */
    public function editCategory()
    {   //查询这个分类是否存在
        $categoryModel = Category::getCategory(\YunShop::request()->id);
        //判断是否有父类id没有默认0
        $parent_id = \YunShop::request()->parent_id ? \YunShop::request()->parent_id : '0';

        if(!$categoryModel){
            return $this->message('无此记录或已被删除','','error');
        }
        //URL地址
        $url = Url::absoluteWeb('goods.category.index',['parent_id'=>$categoryModel->parent_id]);
        if(!empty($categoryModel->parent_id)) {
            //查出父分类
            $parent = Category::getCategory($categoryModel->parent_id);
        }
        if (isset($categoryModel->filter_ids)) {
            $filter_ids = explode(',', $categoryModel->filter_ids);
            $label_group = Filtering::categoryLabel($filter_ids)->get();
        }
        //获取分类发送过来的值
        $requestCategory = \YunShop::request()->category;
        if($requestCategory) {
            if (isset($requestCategory['filter_ids']) && is_array($requestCategory['filter_ids'])) {
                $requestCategory['filter_ids'] = implode(',', $requestCategory['filter_ids']);
            }
            //将数据赋值到model
            $categoryModel->fill($requestCategory);
            //字段检测
            $validator = $categoryModel->validator();
            if ($validator->fails()) {
                //检测失败
                $this->error($validator->messages());
            } else {
                //数据保存
                if ($categoryModel->save()) {
                    //显示信息并跳转
                    return $this->message('分类保存成功', $url);
                }else{
                    $this->error('分类保存失败');
                }
            }
        }

        return view('goods.category.info', [
            'item' => $categoryModel,
            'level' => $categoryModel->level,
            'label_group' => $label_group,
            'parent' => $parent,
        ])->render();
    }

    /**
     * 删除商品分类
     */
    public function deletedCategory()
    {
        $category = Category::getCategory(\YunShop::request()->id);
        if (!$category) {
            return $this->message('无此分类或已经删除','','error');
        }
        //查询是否有商品分类关联表 find_in_set
        $GoodsCategory = GoodsCategory::whereRaw('FIND_IN_SET(?,category_ids)', \YunShop::request()->id)->get();
        $goodsId = [];
        foreach($GoodsCategory as $row){
            $goodsId[] = $row['attributes']['goods_id'];
        }
        //查询是否有商品
        $goods = Goods::wherein('id',$goodsId)->first();
        if(!empty($goods)){
            return $this->message('分类下存在商品,不允许删除','','error');
        }
        $result = Category::deletedAllCategory(\YunShop::request()->id);
        if($result) {
            return $this->message('删除分类成功',Url::absoluteWeb('goods.category.index'));
        }else{
            return $this->message('删除分类失败','','error');
        }
    }


    /**
     * 获取搜索分类
     * @return html
     */
    public function getSearchCategorys()
    {
        $keyword = \YunShop::request()->keyword;
        $categorys = Category::getCategorysByName($keyword);
        return view('goods.category.query', [
            'categorys' => $categorys
        ])->render();
    }

}