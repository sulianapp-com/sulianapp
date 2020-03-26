<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/20
 * Time: 上午10:41
 */

namespace app\common\models;

use app\backend\models\BackendModel;

class PayAccessLog extends BackendModel
{
    public $table = 'yz_pay_access_log';

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = ['uniacid', 'member_id', 'url', 'http_method', 'ip'];
}