<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/30
 */
namespace app\frontend\modules\goods\controllers;

use app\common\components\ApiController;
use app\common\helpers\Url;
use app\common\models\SearchFiltering;
use app\common\models\Category;


class FilteringController extends ApiController
{
    
    public function index()
    {

    	$category = intval(\YunShop::request()->category);

        $category__filter_ids = [];
    	if (isset($category)) {
    		$category__filter_ids = $this->categoryLabel($category);
    	}

        $filtering = SearchFiltering::select('id', 'parent_id', 'name')->getFilterGroup()->categoryLabel($category__filter_ids)->get();


        foreach ($filtering as $key => &$value) {
            $value['value'] = SearchFiltering::select('id', 'parent_id', 'name')->getFilterGroup($value->id)->get()->toArray();
        }
        return $this->successJson('获取过滤数据', $filtering->toArray());
    }

    private function categoryLabel($id)
    {
    	$category = Category::select('id', 'filter_ids')->find($id);

    	return empty($category->filter_ids) ? [] : explode(',', $category->filter_ids);
    }
}