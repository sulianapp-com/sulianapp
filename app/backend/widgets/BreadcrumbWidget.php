<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 08/03/2017
 * Time: 16:28
 */

namespace app\backend\widgets;


use app\common\components\Widget;

class BreadcrumbWidget extends Widget
{
    public $title = '';
    public $breadcrumbs = [];

    public function run()
    {

        return $this->render('breadcrumb',[
            'title'=>$this->title,
            'breadcrumbs'=>$this->breadcrumbs
        ]);
    }
}