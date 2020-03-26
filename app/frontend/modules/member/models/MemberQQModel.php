<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/23
 * Time: 上午10:47
 */

/**
 * QQ登录表
 */
namespace app\frontend\modules\member\models;

use app\backend\models\BackendModel;

class MemberQQModel extends BackendModel
{
    public $table = 'yz_member_qq';

    public static function insertData($data)
    {
        self::insert($data);
    }
}