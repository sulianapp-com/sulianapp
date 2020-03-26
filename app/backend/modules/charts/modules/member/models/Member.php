<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/12 上午11:01
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\charts\modules\member\models;


use app\common\scopes\UniacidScope;

class Member extends \app\backend\modules\member\models\Member
{

//    public static function boot()
//    {
//        parent::boot();
//        self::addGlobalScope(new UniacidScope);
//    }

    //手机号检索

    public function scopeHasMobile($query)
    {
        return $query->where('mobile', '<>', '');
    }

    public function scopeNoHasMobile($query)
    {
        return $query->where('mobile', '');
    }

    //性别检索
    public function scopeManSex($query)
    {
        return $query->where('gender', '1');
    }

    public function scopeFemaleSex($query)
    {
        return $query->where('gender', '2');
    }

    public function scopeUnknownSex($query)
    {
        return $query->where('gender', 0);
    }




}
