<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/11 下午10:36
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\common\scopes;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UniacidScope implements Scope
{

    public function apply(Builder $builder, Model $model)
    {
        return $builder->where('uniacid',\YunShop::app()->uniacid);
    }

}
