<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/17
 * Time: 下午4:10
 */

namespace app\frontend\modules\finance\models;

use \app\common\models\finance\BalanceRecharge as Recharge;
use Illuminate\Database\Eloquent\Builder;

class BalanceRecharge extends Recharge
{
    //设置全局作用域
    public static function boot()
    {
        parent::boot();
        static::addGlobalScope('member_id',function (Builder $builder) {
            return $builder->where('member_id',\YunShop::app()->getMemberId());
        });
    }



    /**
     * 定义字段名
     *
     * @return array */
    public  function atributeNames() {
        return [
            'uniacid'   => "公众号ID不能为空",
            'member_id' => "会员ID不能为空",
            //'old_money' => '余额必须是有效的数字',
            'money'     => '充值金额必须是有效的数字，允许两位小数',
            'new_money' => '计算后金额必须是有效的数字',
            'type'      => '未找到支付方式',
            'ordersn'   => '充值订单号不能为空',
            'status'    => '状态不能为空'
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
            //'old_money' => 'numeric',
            'money'     => 'numeric|regex:/^(?!0+(?:\.0+)?$)\d+(?:\.\d{1,2})?$/', //大于0，允许两位小数
            'new_money' => 'numeric',
            //'type'      => 'regex:/^[126789]$/',              //只能匹配1，2 【1微信，2支付宝】
            'type'      => 'numeric',
            'ordersn'   => 'required',
            'status'    => 'required'
        ];
    }

}
