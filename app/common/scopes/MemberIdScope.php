<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/12 下午7:29
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\common\scopes;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class MemberIdScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        return $builder->where('member_id',\YunShop::app()->getMemberId());
    }

}
