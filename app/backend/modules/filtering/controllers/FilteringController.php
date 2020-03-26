<?php

namespace app\backend\modules\filtering\controllers;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\backend\modules\filtering\models\Filtering;

/**
* 
*/
class FilteringController extends BaseController
{
    protected $pageSize = 15;

    public function index()
    {

        $list = Filtering::getList()->paginate($this->pageSize)->toArray();

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);

        return view('filtering.index', [
            'list' => $list['data'],
            'pager' => $pager,
        ])->render();
    }

    
    public function FilterValue()
    {
        $filteringGroup = Filtering::find(\YunShop::request()->parent_id);
        if(!$filteringGroup) {
            return $this->message('无此标签组或已经删除','','error');
        }

        $filteringValue = Filtering::getList(\YunShop::request()->parent_id)->paginate($this->pageSize)->toArray();

        $pager = PaginationHelper::show($filteringValue['total'], $filteringValue['current_page'], $filteringValue['per_page']);

        return view('filtering.value', [
            'list' => $filteringValue['data'],
            'pager' => $pager,
            'parent' => $filteringGroup,
        ])->render();
    }


    public function create()
    {
        $parent_id = \YunShop::request()->parent_id ? \YunShop::request()->parent_id : '0';

        $url = Url::absoluteWeb('filtering.filtering.index');
        if ($parent_id) {
            $parent = Filtering::find($parent_id);
            if(!$parent) {
                return $this->message('无此标签组或已经删除','','error');
            }

            $url = Url::absoluteWeb('filtering.filtering.filter-value', ['parent_id' => $parent_id]);
        }

        $filter = \YunShop::request()->filter;
       if ($filter) {
            $filtering = new Filtering();

            $filtering->parent_id = $parent_id;
            $filtering->fill($filter);
            $filtering->uniacid = \YunShop::app()->uniacid;
            $validator = $filtering->validator();
                if ($validator->fails()) {
                    //检测失败
                    $this->error($validator->messages());
                } else {
                    //数据保存
                    if ($filtering->save()) {
                        //显示信息并跳转
                        return $this->message('创建成功', $url);
                    }else{
                        $this->error('创建失败');
                    }
                }
        }

        return view('filtering.form', [
            'item' => $filtering,
            'parent_id' => $parent_id,
            'parent' => isset($parent) ? $parent : collect([]),
        ])->render();
    }

    public function edit()
    {
        $filtering = Filtering::find(\YunShop::request()->id);
        if(!$filtering){
            return $this->message('无此记录或已被删除','','error');
        }
        $url = Url::absoluteWeb('filtering.filtering.index');
        $parent_id = $filtering->parent_id;
        if ($parent_id) {
            $parent = Filtering::find($parent_id);
            if(!$parent) {
                return $this->message('无此标签组或已经删除','','error');
            }
            $url = Url::absoluteWeb('filtering.filtering.filter-value', ['parent_id' => $parent_id]);
        }

        $filter = \YunShop::request()->filter;
        if ($filter) {
            $filtering->fill($filter);
            $filtering->uniacid = \YunShop::app()->uniacid;
            $validator = $filtering->validator();
                if ($validator->fails()) {
                    //检测失败
                    $this->error($validator->messages());
                } else {
                    //数据保存
                    if ($filtering->save()) {
                        //显示信息并跳转
                        return $this->message('修改成功', $url);
                    }else{
                        $this->error('修改失败');
                    }
                }
        }
        return view('filtering.form', [
            'item' => $filtering,
            'parent_id' => $parent_id,
            'parent' => isset($parent) ? $parent : collect([]),
        ])->render();
    }

      /**
     * 获取搜索标签组
     * @return html
     */
    public function getSearchLabel()
    {

        $keyword = \YunShop::request()->keyword;
        $filter_group = Filtering::searchFilterGroup($keyword);
        return view('filtering.query', [
            'filter_group' => $filter_group->toArray(),
        ])->render();
    }

    public function del()
    {
        $filtering = Filtering::find(\YunShop::request()->id);
        if(!$filtering) {
            return $this->message('无此数据或已经删除','','error');
        }

        $result = Filtering::del(\YunShop::request()->id);
      
        if($result) {
            return $this->message('删除成功',Url::absoluteWeb('filtering.filtering.index'));
        }else{
            return $this->message('删除失败','','error');
        }

    }

}