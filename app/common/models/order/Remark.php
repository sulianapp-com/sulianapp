<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/6
 * Time: 下午7:54
 */
namespace app\common\models\order;


use app\common\models\BaseModel;

class Remark extends BaseModel
{
    public $table = 'yz_order_remark';
    protected $guarded = [''];
}