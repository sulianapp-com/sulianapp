<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/9/4 上午10:52
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\common\models\finance;


use app\common\models\BaseModel;
use app\common\scopes\UniacidScope;
use app\common\traits\CreateOrderSnTrait;

class PointTransfer extends BaseModel
{
    use CreateOrderSnTrait;

    protected $table = 'yz_point_transfer';

    protected $guarded =[''];

    public static function boot()
    {
        parent::boot();
        self::addGlobalScope(new UniacidScope);
    }


    /**
     * 定义字段名
     *
     * @return array */
    public  function atributeNames() {
        return [
            'uniacid'   => "公众号",
            'transferor'=> "转让者",
            'recipient' => '被转让者',
            'money'     => '转让金额',
            'status'    => '状态'
        ];
    }

    /**
     * 字段规则
     *
     * @return array */
    public  function rules()
    {
        return [
            'uniacid'   => "required",
            'transferor'=> "required",
            'recipient' => 'required',
            'money'     => 'numeric|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            'status'    => 'required'
        ];
    }
}
