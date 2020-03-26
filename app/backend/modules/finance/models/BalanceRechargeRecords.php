<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/19
 * Time: 下午3:56
 */

namespace app\backend\modules\finance\models;


use app\common\models\finance\BalanceRecharge;
use app\common\services\PayFactory;

class BalanceRechargeRecords extends BalanceRecharge
{
    protected $appends = ['type_name'];

    /**
     * Payment translation set.
     *
     * @var array
     */
    private static $typeComment = [
        self::PAY_TYPE_SHOP         => "后台充值",
        PayFactory::PAY_WEACHAT     => "微信支付",
        PayFactory::PAY_ALIPAY      => "支付宝",
        PayFactory::PAY_APP_WEACHAT => "APP-微信",
        PayFactory::PAY_APP_ALIPAY  => "APP-支付宝",
    ];

    /**
     * Gets the value of the additional field type_name.
     *
     * @return string
     */
    public function getTypeNameAttribute()
    {
        return static::getTypeNameComment($this->attributes['type']);
    }

    /**
     * Gets the value of the additional field type_name.
     *
     * @param $attributes
     * @return string
     */
    public function getTypeNameComment($attributes)
    {
        return isset(static::$typeComment[$attributes]) ? static::$typeComment[$attributes] : "其他支付";
    }


    //todo 以下代码未检查


    public function scopeRecords($query)
    {
        $query->withMember();
    }

    public function scopeSearch($query, $search)
    {
        if ($search['ordersn']) {
            $query->where('ordersn', 'like', $search['ordersn'] . '%');
        }

        //todo 下面条件需要找到对应类魔术方法
        if ($search['realname'] || $search['level_id'] || $search['group_id']) {
            $query = $query->whereHas('member', function($member)use($search) {
                if ($search['realname']) {
                    $member = $member->select('uid', 'nickname','realname','mobile','avatar')
                        ->where('realname', 'like', '%' . $search['realname'] . '%')
                        ->orWhere('mobile', 'like', '%' . $search['realname'] . '%')
                        ->orWhere('nickname', 'like', '%' . $search['realname'] . '%')
                        ->orWhere('uid', $search['realname']);
                }
                if ($search['level_id']) {
                    $member = $member->whereHas('yzMember', function ($level)use($search) {
                        $level->where('level_id', $search['level_id']);
                    });
                }
                if ($search['group_id']) {
                    $member = $member->whereHas('yzMember', function ($group)use($search) {
                        $group->where('group_id', $search['group_id']);
                    });
                }

            });
        }

        if ($search['searchtime']) {
            $query = $query->whereBetween('updated_at', [strtotime($search['time_range']['start']),strtotime($search['time_range']['end'])]);
        }
        return $query;
    }


}
