<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/11 下午9:32
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\finance\models;



class Balance extends \app\common\models\finance\Balance
{

    public function scopeRecords($query)
    {
        return $query->withMember();
    }

}