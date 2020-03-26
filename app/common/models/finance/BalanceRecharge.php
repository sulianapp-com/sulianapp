<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/1
 * Time: 上午11:14
 */

namespace app\common\models\finance;


use app\common\models\BaseModel;
use app\common\scopes\UniacidScope;
use app\common\traits\CreateOrderSnTrait;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property double $money
 *
 * Class BalanceRecharge
 * @package app\common\models\finance
 */
class BalanceRecharge extends BaseModel
{
    use CreateOrderSnTrait;


    protected $table = 'yz_balance_recharge';

    protected $guarded = [''];


    /**
     * Payment method background recharge.
     */
    const PAY_TYPE_SHOP = 0;

    /**
     * Recharge state success.
     */
    const PAY_STATUS_SUCCESS = 1;

    /**
     * Recharge state error.
     */
    const PAY_STATUS_ERROR = -1;


    public static function boot()
    {
        parent::boot();
        static::addGlobalScope( new UniacidScope);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function member()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'member_id');
    }


    /**
     * 检索条件，订单号检索
     * @param $query
     * @param $orderSn
     * @return mixed
     */
    public function scopeOfOrderSn($query,$orderSn)
    {
        return $query->where('ordersn',$orderSn);
    }


    public function scopeWithMember($query)
    {
        return $query->with(['member' => function($query) {
            return $query->select('uid', 'nickname','realname','mobile','avatar')
                ->with(['yzMember' => function($memberInfo) {
                    return $memberInfo->select('member_id', 'group_id', 'level_id')
                        ->with(['level' => function($level) {
                            return $level->select('id','level_name');
                        }])
                        ->with(['group'=> function($group) {
                            return $group->select('id', 'group_name');
                        }]);
                }]);
        }]);
    }





    //todo 以下代码需要重构
    /*
     *
     *
     * */
    public static function getMemberRechargeRecord($memberId)
    {
        return self::uniacid()->select('id','money', 'type', 'created_at')->where('member_id', $memberId)->get();
    }

    /*
     * 通过记录ID值获取记录
     *
     * @params int $recordId 记录ID
     *
     * @return object
     * @Author yitian */
    public static function getRechargeRecordByid($recordId)
    {
        return self::uniacid()->where('id', $recordId)->first();
    }

    /*
    * 通过记录 ordersn 值获取记录
    *
    * @params int $recordId 记录ID
    *
    * @return object
    * @Author yitian */
    public static function getRechargeRecordByOrdersn($ordersn)
    {
        return self::withoutGlobalScope('member_id')->where('ordersn', $ordersn)->first();
    }

    /*
     * 获取充值记录分页列表
     *
     * return object
     *
     * @Author yitian */
    public static function getPageList($pageSize)
    {
        return self::uniacid()
            ->with(['member' => function($query) {
                return $query->select('uid', 'nickname','realname','mobile','avatar')
                    ->with(['yzMember' => function($memberInfo) {
                        return $memberInfo->select('member_id', 'group_id', 'level_id')
                            ->with(['level' => function($level) {
                                return $level->select('id','level_name');
                            }])
                            ->with(['group'=> function($group) {
                                return $group->select('id', 'group_name');
                            }]);
                }]);
            }])
            ->orderBy('created_at', 'desc')
            ->paginate($pageSize);
    }

    /*
     * 搜索充值记录分页列表
     *
     * @params int $pageSize
     * @params array $search
     * return object
     * */
    public static function getSearchPageList($pageSize, $search =[])
    {
        $query = self::uniacid();
        if ($search['ordersn']) {
            $query = $query->where('ordersn', 'like', $search['ordersn'] . '%');
        }
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


        return $query->orderBy('created_at', 'desc')->paginate($pageSize);
    }

    /*
     * 验证订单号是否存在，存在返回true
     *
     * @params varchar $orderSN
     *
     * @return bool true or false
     *
     * @Author yitian */
    public static function validatorOrderSn($orderSN)
    {
        return self::uniacid()->where('ordersn', $orderSN)->first();
    }

    /**
     * 定义字段名
     *
     * @return array */
    public  function atributeNames() {
        return [
            'uniacid'   => "公众号ID",
            'member_id' => "会员ID",
            //'old_money' => '余额必须是有效的数字',
            'money'     => '充值金额',
            'new_money' => '计算后金额',
            'type'      => '充值类型',
            'ordersn'   => '充值订单号',
            'status'    => '状态',
            'remark'    => '备注信息'
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
            'money'     => 'numeric|regex:/^[\-\+]?\d+(?:\.\d{1,2})?$/|max:9999999999',
            'new_money' => 'numeric',
            'type'      => 'required',
            'ordersn'   => 'required',
            'status'    => 'required',
            'remark'    => 'max:50'
        ];
    }




}
