<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/11/21 下午4:07
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\common\models\finance;


use app\common\models\BaseModel;

class PointLoveSet extends BaseModel
{
    protected $table = 'yz_point_love_set';

    protected $guarded = [];





    public function rules()
    {
        return [
            'rate'      => 'numeric|regex:/^[\-\+]?\d+(?:\.\d{1,2})?$/|max:100|min:-1'
        ];
    }

    public function atributeNames()
    {
        return [
            'rate'      => '转入比例'
        ];
    }

}
