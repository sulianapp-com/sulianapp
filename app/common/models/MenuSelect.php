<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 02/05/2017
 * Time: 10:49
 */

namespace app\common\models;


class MenuSelect extends Menu
{
    public function getTreeAllNodes()
    {
        return self::where('status', 1)
            ->orderBy('sort', 'asc')->get();
    }
}