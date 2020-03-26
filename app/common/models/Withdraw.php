<?php
/**
 * Created by PhpStorm.
 * Class Withdraw
 * Author: Yitan
 * Date: 2017/11/06
 * @package app\common\models
 */

namespace app\common\models;


use app\common\traits\CreateOrderSnTrait;

/**
 * @property int $id
 * @property int $member_id
 * @property int $status
 * @property int $type_id
 * @property int $arrival_at
 * @property string $type_name
 * @property string $withdraw_sn
 * @property double $poundage
 * @property double $amounts
 * @property double $actual_amounts
 *
 * @property Member $hasOneMember
 *
 * @method self initial()
 * @method self audit()
 * @method self paying()
 * @method self payed()
 * @method self rebut()
 * @method self invalid()
 *
 * Class Withdraw
 * @package app\common\models
 */
class Withdraw extends BaseModel
{
    use CreateOrderSnTrait;

    /**
     * 提现审核状态：无效
     */
    const STATUS_INVALID    = -1;


    /**
     * 提现审核状态：未审核
     */
    const STATUS_INITIAL    = 0;


    /**
     * 提现审核状态：待打款
     */
    const STATUS_AUDIT      = 1;


    /**
     * 提现审核状态：已打款
     */
    const STATUS_PAY        = 2;


    /**
     * 提现审核状态：已驳回
     */
    const STATUS_REBUT      = 3;


    /**
     * 提现审核状态：打款中
     */
    const STATUS_PAYING     = 4;


    /**
     * 提现打款方式：打款至余额
     */
    const WITHDRAW_WITH_BALANCE = 'balance';


    /**
     * 提现打款方式：打款至微信
     */
    const WITHDRAW_WITH_WECHAT  = 'wechat';


    /**
     * 提现打款方式：打款至支付宝
     */
    const WITHDRAW_WITH_ALIPAY  = 'alipay';


    /**
     * 提现打款方式：手动打款
     */
    const WITHDRAW_WITH_MANUAL  = 'manual';


    /**
     * 提现打款方式：打款到环迅
     */
    const WITHDRAW_WITH_HUANXUN  = 'huanxun';


    /**
     * 提现打款方式：打款到EUP
     */
    const WITHDRAW_WITH_EUP_PAY  = 'eup_pay';

    /**
     * @author blank
     * 提现打款方式：打款到易宝
     */
    const WITHDRAW_WITH_YOP  = 'yop_pay';


    const WITHDRAW_WITH_SEPARATE_UNION_PAY = 'separate';

    /**
     * 提现打款方式：打款到汇聚
     */
    const WITHDRAW_WITH_CONVERGE_PAY  = 'converge_pay';


    /**
     * 手动打款方式：手动至银行卡
     */
    const MANUAL_TO_BANK    = 1;


    /**
     * 手动打款方式：手动至微信
     */
    const MANUAL_TO_WECHAT  = 2;


    /**
     * 手动打款方式：手动至支付宝
     */
    const MANUAL_TO_ALIPAY  = 3;


    /**
     * 审核通过的收入 ids 集合
     * 
     * @var array
     */
    public $audit_ids = [];


    /**
     * 审核驳回的收入 ids 集合
     * 
     * @var array
     */
    public $rebut_ids = [];


    /**
     * 审核无效的收入 ids 集合
     * 
     * @var array
     */
    public $invalid_ids = [];


    /**
     * 提现打款方式集合
     *
     * @var array
     */
    public static $payWayComment = [
        self::WITHDRAW_WITH_BALANCE                => '提现到余额',
        self::WITHDRAW_WITH_WECHAT                 => '提现到微信',
        self::WITHDRAW_WITH_ALIPAY                 => '提现到支付宝',
        self::WITHDRAW_WITH_MANUAL                 => '提现手动打款',
        self::WITHDRAW_WITH_HUANXUN                => '提现到银行卡',
        self::WITHDRAW_WITH_EUP_PAY                => '提现EUP',
        self::WITHDRAW_WITH_YOP                    => '提现易宝',
        self::WITHDRAW_WITH_SEPARATE_UNION_PAY     => '提现银联',
        self::WITHDRAW_WITH_CONVERGE_PAY           => '提现到银行卡-HJ',
    ];


    /**
     * 提现审核状态集合
     *
     * @var array
     */
    public static $statusComment = [
        self::STATUS_INVALID    => '已无效',
        self::STATUS_INITIAL    => '待审核',
        self::STATUS_AUDIT      => '待打款',
        self::STATUS_PAY        => '已打款',
        self::STATUS_REBUT      => '已驳回',
        self::STATUS_PAYING     => '打款中',
    ];


    /**
     * 数据表名称
     * 
     * @var string
     */
    protected $table = 'yz_withdraw';


    /**
     * @var array
     */
    protected $guarded = [];


    /**
     * @var array
     */
    protected $appends = ['status_name', 'pay_way_name'];


    public function hasOneMember()
    {
        return $this->hasOne(Member::class, 'uid', 'member_id');
    }


    public function hasOneYzMember()
    {
        return $this->hasOne('app\backend\modules\member\models\MemberShopInfo', 'member_id', 'member_id');

    }


    public function bankCard()
    {
        return $this->hasOne('app\common\models\member\BankCard', 'member_id', 'member_id');
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
     * 通过 $pay_way 值获取 $pay_way 名称
     *
     * @param $pay_way
     * @return mixed|string
     */
    public static function getPayWayComment($pay_way)
    {
        return isset(static::$payWayComment[$pay_way]) ? static::$payWayComment[$pay_way] : '';
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
     * 通过字段 pay_way 输出 pay_way_name ;
     *
     * @return string
     */
    public function getPayWayNameAttribute()
    {
        return static::getPayWayComment($this->attributes['pay_way']);
    }

    /**
     * 待审核状态
     *
     * @param $query
     */
    public function scopeInitial($query)
    {
        $query->where('status', self::STATUS_INITIAL);
    }

    /**
     * 待打款状态
     *
     * @param $query
     */
    public function scopeAudit($query)
    {
        $query->where('status', self::STATUS_AUDIT);
    }

    /**
     * 打款中状态
     *
     * @param $query
     */
    public function scopePaying($query)
    {
        $query->where('status', self::STATUS_PAYING);
    }

    /**
     * 已打款状态
     *
     * @param $query
     */
    public function scopePayed($query)
    {
        $query->where('status', self::STATUS_PAY);
    }

    /**
     * 已驳回状态
     *
     * @param $query
     */
    public function scopeRebut($query)
    {
        $query->where('status', self::STATUS_REBUT);
    }

    /**
     * 已无效状态
     *
     * @param $query
     */
    public function scopeInvalid($query)
    {
        $query->where('status', self::STATUS_INVALID);
    }


    public function scopeRecords($query)
    {
        $types = static::getIncomeTypes();

        return $query->uniacid()->whereIn('type', $types);
    }

    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOfWithdrawSn($query, $withdraw_sn)
    {
        return $query->where('withdraw_sn', $withdraw_sn);
    }

    public function atributeNames()
    {
        return [
            'member_id'     => '会员ID',
            'type'          => '提现类型',
            'amounts'       => '提现金额',
            'pay_way'       => '打款方式',
        ];
    }

    public function rules()
    {
        return  [
            'member_id'     => 'required',
            'type'          => 'required',
            'amounts'       => 'required',
            'pay_way'       => 'required',
        ];
    }



    /**
     * todo 应该剔出本类
     *
     * 获取已开启插件 type 字段集
     *
     * @return array
     */
    public static function getIncomeTypes()
    {
        $configs = \app\backend\modules\income\Income::current()->getItems();

        $types = [];
        foreach ($configs as $config) {
            $types[] = $config['class'];
        }
        return $types;
    }





/********************* todo 以下代码不确定功能逻辑，需要处理删除 yitian 2017-12-19 ****************/









    public $separate = [];

    public $attributes = [];

    public $StatusService;

    public $PayWayService;

    public $TypeData;


    /**
     * @return string
     */
    public function getTypeDataAttribute()
    {

        if (!isset($this->TypeData)) {
            $configs = \app\backend\modules\income\Income::current()->getItems();

            foreach ($configs as $key => $config) {
                if ($config['class'] === $this->type) {

                    $orders = Income::getIncomeByIds($this->type_id)->get();
//                    $is_pay = Income::getIncomeByIds($this->type_id)->where('pay_status','1')->get()->sum(amount);
                    if($orders){
                        $this->TypeData['income_total'] = $orders->count();
//                        $this->TypeData['is_pay'] = $is_pay;
                        $this->TypeData['incomes'] = $orders->toArray();

//                        foreach ($orders as $k => $order) {
////                            $this->TypeData['orders'][$k] = $order->incometable->ordertable->toArray();
//                            $this->TypeData['incomes'][$k] = $order->incometable->toArray();
//                        }

                    }
                }


            }
        }
        return $this->TypeData;
    }


    public static function getWithdrawByWithdrawSN($withdrawSN)
    {
        return self::uniacid()->where('withdraw_sn',$withdrawSN)->first();
    }

    public static function getBalanceWithdrawById($id)
    {
        return self::uniacid()->where('id', $id)
            ->with(['hasOneMember' => function($query) {
                return $query->select('uid', 'mobile', 'realname', 'nickname', 'avatar')
                    ->with(['yzMember' => function($member) {
                        return $member->select('member_id', 'group_id','alipay','wechat')
                            ->with(['group' => function($group) {
                                return $group->select('id', 'group_name');
                            }]);
                    }]);
            }])
            ->with(['bankCard'=> function($bank){
                return $bank->select('member_id','bank_card','member_name','bank_card','bank_province','bank_city','bank_branch','bank_name');
            }])
            ->first();

    }
    public static function getWithdrawById($id)
    {
        $Model = self::where('id', $id);
        $Model->orWhere('withdraw_sn',$id);
        $Model->with(['hasOneMember' => function ($query) {
            $query->select('uid', 'mobile', 'realname', 'nickname', 'avatar');
        }]);
//        $Model->with(['hasOneAgent' => function ($query) {
//            $query->select('member_id', 'agent_level_id', 'commission_total');
//        }]);

        return $Model;
    }




//    public function hasOneAgent()
//    {
//        return $this->hasOne('Yunshop\Commission\models\Agents', 'member_id', 'member_id');
//    }

    public static function updatedWithdrawStatus($id, $updatedData)
    {
        return self::where('id',$id)
            ->orWhere('withdraw_sn',(string)$id)
            ->update($updatedData);
    }

    //统计当天的
    public static function  successfulWithdrawals($pay_type,$start,$end){

        return self::where([
            ['member_id',\YunShop::app()->getMemberId()],
            ['status','=',self::STATUS_PAY],
            ['pay_way','=',$pay_type],
            ['pay_at','>=',$start],
            ['pay_at','<=',$end]
        ])->count();

    }


    public static function SuccessfulCashWithdrawal($pay_type,$start,$end){

        return self::where([
                ['member_id',\YunShop::app()->getMemberId()],
                ['status','=',self::STATUS_PAY],
                ['pay_way','=',$pay_type],
                ['pay_at','>=',$start],
                ['pay_at','<=',$end]
            ])->sum('actual_amounts');

    }



}