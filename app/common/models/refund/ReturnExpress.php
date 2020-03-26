<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/23
 * Time: 上午10:49
 */

namespace app\common\models\refund;

use app\common\models\BaseModel;

class ReturnExpress extends BaseModel
{
    protected $fillable = [];
    protected $guarded = ['id'];
    public $table = 'yz_return_express';

}