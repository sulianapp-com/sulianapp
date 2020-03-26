<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/20 上午9:36
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\charts\controllers;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;

class ChartsController extends BaseController
{

    protected $page_size = 10;



    public function arrayKrSort(array $data, $field)
    {
        $data = array_values(array_sort($data, function ($value) use ($field) {
            return $value[$field];
        }));
        krsort($data);
        return  array_values($data);
    }



    protected function getPagination(array $data)
    {
       return PaginationHelper::show(sizeof($data) - $this->page_size, $this->getPage(), $this->page_size);
    }




    protected function getPageData(array $data)
    {
        $start = $this->getPage() * $this->page_size - $this->page_size;
        $end = $start + $this->page_size;

        $data = array_where($data, function ($value, $key) use($start,$end) {
            return $key >= $start && $key < $end;
        });
        return $data;
    }



    protected function getPage()
    {
        return \YunShop::request()->page ?: 1;
    }


}
