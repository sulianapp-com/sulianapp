<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/19 下午2:08
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\charts\modules\order\models;


class Member extends \app\backend\modules\charts\modules\member\models\Member
{

    public function hasManyOrder()
    {
        return $this->hasMany('app\common\models\Order', 'uid', 'uid');
    }


}
