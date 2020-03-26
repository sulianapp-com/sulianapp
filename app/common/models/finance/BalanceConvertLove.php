<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/2
 * Time: 下午2:25
 */

namespace app\common\models\finance;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

/*
 * 余额转化爱心值
 *
 * */
class BalanceConvertLove extends BaseModel
{
    public $table = 'yz_balance_covert_love';

    protected $guarded = [''];

    const CONVERT_STATUS_SUCCES = 1;

    const CONVERT_STATUS_ERROR = -1;


    /**
     * 设置全局作用域 拼接 uniacid()
     */
    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(
            function (Builder $builder) {
                return $builder->uniacid();
            }
        );
    }

    /**
     * 定义字段名
     *
     * @return array */
    public  function atributeNames() {
        return [
            'uniacid'   => "公众号ID",
            'member_id' => "会员ID",
            'covert_amount' => '转化金额',
            'status'    => '状态',
            'order_sn'  => '订单号',
            'remark' => '备注'
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
            'member_id' => "required",
            'covert_amount' => 'required',
            'status'    => 'required',
            'order_sn'  => 'required',
            'remark' => 'required'
        ];
    }
}