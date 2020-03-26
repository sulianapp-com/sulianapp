<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/29
 * Time: 下午5:23
 */

namespace app\common\models\finance;


use app\common\models\BaseModel;

use app\common\observers\balance\BalanceChangeObserver;
use app\common\services\credit\ConstService;
use Illuminate\Database\Eloquent\Builder;


/*
 * 余额变动记录表
 *
 * */
class Balance extends BaseModel
{
    public $table = 'yz_balance';

    protected $guarded= [''];

    protected $appends = ['service_type_name','type_name'];

    /**
     * 设置全局作用域 拼接 uniacid()
     */
    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            return $builder->uniacid();
        });
        self::observe(BalanceChangeObserver::class);
    }

    /**
     * 模型关联，关联会员数据表
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function member()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'member_id');
    }

    /**
     * 模型关联，关联余额转让记录表
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Transfer()
    {
        return $this->belongsTo('app\common\models\finance\BalanceTransfer', 'order_sn', 'serial_number');
    }

    /**
     * @param $balance
     * @return mixed|string
     */
    public static function getBalanceComment($balance)
    {
        $balanceComment = static::getSourceComment();
        return isset($balanceComment[$balance]) ? $balanceComment[$balance]: '';
    }

    /**
     * @param $balance
     * @return mixed|string
     */
    public static function getTypeNameComment($balance)
    {
        return isset(static::$type_name[$balance]) ? static::$type_name[$balance]: '';
    }

    /**
     * 通过字段 service_type 输出 service_type_name ;
     * @return string
     */
    public function getServiceTypeNameAttribute()
    {
        return static::getBalanceComment($this->attributes['service_type']);
    }

    /**
     * 通过字段 type 输出 type_name 名称
     * @return mixed|string
     */
    public function getTypeNameAttribute()
    {
        return static::getTypeNameComment($this->attributes['type']);
    }

    /**
     * 检索条件 服务类型
     * @param $query
     * @param $source
     * @return mixed
     */
    public function scopeOfSource($query, $source)
    {
        return $query->where('service_type', $source);
    }

    /**
     * 检索条件 会员ID
     * @param $query
     * @param $memberId
     * @return mixed
     */
    public function scopeOfMemberId($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    /**
     * 检索条件 单号／流水号
     * @param $query
     * @param $orderSn
     * @return mixed
     */
    public function scopeOfOrderSn($query, $orderSn)
    {
        return $query->where('serial_number', $orderSn);
    }

    public function scopeWithMember($query)
    {
        return $query->with(['member' => function($query) {
            return $query->select('uid', 'nickname', 'realname', 'avatar', 'mobile', 'credit2');
        }]);
    }

    public function scopeSearch($query,$search)
    {
        if ($search['source']) {
            $query->ofSource($search['source']);
        }
        if ($search['type']) {
            $query->whereType($search['type']);
        }
        if ($search['order_sn']) {
            $query->where('serial_number', 'like', $search['order_sn'] . '%');
        }
        if ($search['search_time']) {
            $query->whereBetween('created_at', [strtotime($search['time']['start']), strtotime($search['time']['end'])]);
        }
        return $query;
    }

    public function scopeSearchMember($query,$search)
    {
        return $query->whereHas('member',function($query)use($search) {
            return $query->search($search);
        });
    }







    /**
     * 获取分页列表
     * @param $pageSize
     * @return mixed
     */
    public static function getPageList($pageSize)
    {
        return self::uniacid()
            ->with(['member' => function($query) {
                return $query->select('uid', 'nickname', 'realname', 'avatar', 'mobile', 'credit2');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate($pageSize);
    }

    public static function getSearchPageList($pageSize, $search)
    {
        $query = static::uniacid();
        if ($search['realname']) {
            $query = $query->whereHas('member', function ($member)use($search) {
                if ($search['realname']) {
                    $member = $member->select('uid', 'nickname','realname','mobile','avatar','credit2')
                        ->where('realname', 'like', '%' . $search['realname'] . '%')
                        ->orWhere('mobile', 'like', '%' . $search['realname'] . '%')
                        ->orWhere('nickname', 'like', '%' . $search['realname'] . '%');
                }
            });
        }
        if ($search['service_type']) {
            $query = $query->where('service_type', $search['service_type']);
        }
        if ($search['searchtime'] == 1) {
            $query = $query->whereBetween('created_at', [strtotime($search['time_range']['start']),strtotime($search['time_range']['end'])]);
        }

        return $query->orderBy('created_at', 'desc')->paginate($pageSize);
    }

    /**
     * 前端接口，通过 type 查看会员余额变动明细
     * @param $memberId
     * @param string $type
     * @return mixed
     * @Author yitian */
    public static function getMemberDetailRecord($memberId, $type= '')
    {
        $query = self::uniacid()->where('member_id',$memberId);
        if ($type == static::TYPE_INCOME || $type == static::TYPE_EXPENDITURE) {
            $query = $query->where('type', $type);
        }
        return $query->orderBy('created_at','desc')->paginate(15);
    }

    /**
     * 通过记录ID获取记录详情
     * @param $id
     * @return mixed
     * @Author yitian */
    public static function getDetailById($id)
    {
        return static::uniacid()->where('id', $id)
            ->with(['member' => function($member) {
                return $member->select('uid', 'nickname', 'realname', 'avatar', 'mobile', 'credit2');
            }])
            ->first();
    }


    public static function getSourceComment()
    {
        return (new ConstService())->sourceComment();
    }

    //2018/5/21 快递单增加余额记录
    public static function getBalanceById($member_id)
    {
        return self::uniacid()->where('member_id', $member_id)->first();
    }





//以下常量、方法已经废弃， 防止报错，暂时未删除，请不要使用
//
//
//



    const OPERATOR_ORDER_   = -1; //操作者 订单

    const OPERATOR_MEMBER   = -2; //操作者 会员

    //类型：收入
    const TYPE_INCOME = 1;

    //类型：支出
    const TYPE_EXPENDITURE = 2;


    const BALANCE_RECHARGE  = 1; //充值

    const BALANCE_CONSUME   = 2; //消费

    const BALANCE_TRANSFER  = 3; //转让

    const BALANCE_DEDUCTION = 4; //抵扣

    const BALANCE_AWARD     = 5; //奖励

    const BALANCE_WITHDRAWAL= 6; //余额提现

    const BALANCE_INCOME    = 7; //提现至余额

    const BALANCE_CANCEL_DEDUCTION  = 8; //抵扣取消余额回滚

    const BALANCE_CANCEL_AWARD      = 9; //奖励取消回滚

    const BALANCE_CANCEL_CONSUME    = 10; //消费取消回滚

    //删除 2017-12-04
   /* public static $balanceComment = [
        self::BALANCE_RECHARGE      => '余额充值',
        self::BALANCE_CONSUME       => '余额消费',
        self::BALANCE_TRANSFER      => '余额转让',
        self::BALANCE_DEDUCTION     => '余额抵扣',
        self::BALANCE_AWARD         => '余额奖励',
        self::BALANCE_WITHDRAWAL    => '余额提现',
        self::BALANCE_INCOME        => '提现至余额',
        self::BALANCE_CANCEL_DEDUCTION      => '抵扣取消回滚',
        self::BALANCE_CANCEL_AWARD          => '奖励取消回滚',
        self::BALANCE_CANCEL_CONSUME        => '消费取消回滚'
    ];*/

    public static $type_name = [
        self::TYPE_INCOME       => '收入',
        self::TYPE_EXPENDITURE  => '支出'
    ];

}
