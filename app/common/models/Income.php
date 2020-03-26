<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/27
 * Time: 下午1:52
 */

namespace app\common\models;


use app\common\observers\income\IncomeObserver;

/**
 * @property int status
 * @property float amount
 * @property string incometable_type
 *
 * Class Income
 * @package app\common\models
 */
class Income extends BaseModel
{
    protected $table = 'yz_member_income';


    protected $guarded = [''];

    protected $appends = ['status_name', 'pay_status_name'];
    /**
     * @var Withdraw withdraw
     */
    private $withdraw;
    /**
     * 提现状态：未提现
     */
    const STATUS_INITIAL = 0;


    /**
     * 提现状态：已提现
     */
    const STATUS_WITHDRAW = 1;


    /**
     * 打款状态：无效
     */
    const PAY_STATUS_INVALID = -1;


    /**
     * 打款状态：未审核
     */
    const PAY_STATUS_INITIAL = 0;


    /**
     * 打款状态：未打款
     */
    const PAY_STATUS_WAIT = 1;


    /**
     * 打款状态：已打款
     */
    const PAY_STATUS_FINISH = 2;


    /**
     * 打款状态：已驳回
     */
    const PAY_STATUS_REJECT = 3;

    /**
     * 分帐插件使用，['order_id' => '', 'mark' => '']
     *
     * @var array
     */
    public $separate = [];


    /**
     * 提现状态名称集合
     *
     * @var array
     */
    public static $statusComment = [
        self::STATUS_INITIAL => '未提现',
        self::STATUS_WITHDRAW => '已提现',
    ];


    /**
     * 打款状态名称集合
     *
     * @var array
     */
    public static $payStatusComment = [
        self::PAY_STATUS_INVALID => '无效',
        self::PAY_STATUS_INITIAL => '未审核',
        self::PAY_STATUS_WAIT => '未打款',
        self::PAY_STATUS_FINISH => '已打款',
        self::PAY_STATUS_REJECT => '已驳回',
    ];

    public static function boot()
    {
        parent::boot();
        self::observe(IncomeObserver::class);
    }

    /**
     * 通过 $status 值获取 $status 名称
     *
     * @param $status
     * @return mixed|string
     */
    public static function getStatusComment($status)
    {
        return isset(static::$statusComment[$status]) ? static::$statusComment[$status] : '';
    }


    /**
     * 通过 $pay_way 值获取 $pay_status 名称
     *
     * @param $pay_status
     * @return mixed|string
     */
    public static function getPayWayComment($pay_status)
    {
        return isset(static::$payStatusComment[$pay_status]) ? static::$payStatusComment[$pay_status] : '';
    }


    /**
     * 通过字段 status 输出 status_name ;
     *
     * @return string
     */
    public function getStatusNameAttribute()
    {
        return static::getStatusComment($this->attributes['status']);
    }


    /**
     * 通过字段 pay_status 输出 pay_status_name ;
     *
     * @return string
     */
    public function getPayStatusNameAttribute()
    {
        return static::getPayWayComment($this->attributes['pay_status']);
    }


    /**
     * 可提现收入检索条件
     *
     * @param $query
     * @return mixed
     */
    public function scopeCanWithdraw($query)
    {
        return $query->where('status', static::STATUS_INITIAL);
    }


    /**
     * 关联会员数据表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member()
    {
        return $this->belongsTo('app\common\models\Member');
    }













    //todo 以下代码未检查 yitian :: 2017-11-14

    /**
     * @param $id
     * @return mixed
     */
    public static function getIncomeFindId($id)
    {
        return self::find($id);
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getIncomeById($id)
    {
        return self::uniacid()
            ->where('id', $id);

    }

    /**
     * @param $ids
     * @return mixed
     */
    public static function getIncomeByIds($ids)
    {
        return self::uniacid()
            ->whereIn('id', explode(',', $ids));
    }


    public function incometable()
    {
        return $this->morphTo();
    }

    /**
     * @return mixed
     */
    public static function getIncomes()
    {
        return self::uniacid();
    }

    public static function getIncomeInMonth($search)
    {
        $model = self::select('create_month');
        $model->uniacid();
        $model->with(['hasManyIncome' => function ($query) use ($search) {
            $query->select('id', 'create_month', 'incometable_type', 'type_name', 'amount', 'created_at');
            if ($search['type']) {
                $query->where('incometable_type', $search['type']);
            }
            $query->where('member_id', \YunShop::app()->getMemberId());
            $query->orderBy('id', 'desc');
            return $query->get();
        }]);
        $model->groupBy('create_month');
        $model->orderBy('create_month', 'desc');
        return $model;
    }

    public static function getDetailById($id)
    {
        $model = self::uniacid();
        $model->select('detail');
        $model->where('id', $id);
        return $model;
    }

    public static function getWithdraw($type, $typeId, $status)
    {
        return self::where('type', 'commission')
            ->where('member_id', \YunShop::app()->getMemberId())
            ->whereIn('id', explode(',', $typeId))
            ->update(['status' => $status]);
    }

    public static function updatedWithdraw($type, $typeId, $status)
    {
        return self::where('member_id', \YunShop::app()->getMemberId())
            ->whereIn('id', explode(',', $typeId))
            ->update(['status' => $status]);
    }

    public static function updatedIncomeStatus($type, $typeId, $status)
    {
        return self::where('member_id', \YunShop::app()->getMemberId())
            ->whereIn('id', explode(',', $typeId))
            ->update(['status' => $status]);
    }

    public function hasManyIncome()
    {
        return $this->hasMany(self::class, "create_month", "create_month");
    }

    public static function updatedIncomePayStatus($id, $updatedData)
    {
        return self::where('id', $id)
            ->update($updatedData);
    }

    public static function getIncomesList($search)
    {
        if($search['select']){
            $selects = ['id', 'create_month', 'incometable_type', 'type_name', 'amount', 'created_at','detail','incometable_id'];
        }else{
            $selects = ['id', 'create_month', 'incometable_type', 'type_name', 'amount', 'created_at'];
        }

        $model = self::uniacid();
        $model->select($selects);
        if ($search['type']) {
            $model->where('incometable_type', $search['type']);
        }
        $model->where('member_id', \YunShop::app()->getMemberId());
        $model->orderBy('id', 'desc');

        return $model;
    }
    
    /**
     * @return Withdraw
     */
    public function withdraw()
    {
        if (!isset($this->withdraw)) {
            $this->withdraw = Withdraw::where('type_id', 'like', "%{$this->id}%")->where('type', $this->incometable_type)->first();
        }
        return $this->withdraw;
    }
}