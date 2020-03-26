<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 上午10:54
 */

namespace app\common\models\goods;


use app\common\exceptions\AppException;
use app\common\models\BaseModel;
use app\common\models\Goods;
use app\common\models\Member;
use app\common\models\MemberGroup;
use app\common\models\MemberLevel;
use app\common\models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class Privilege
 * @package app\common\models\goods
 * @property int goods_id
 * @property string show_levels
 * @property string show_groups
 * @property string buy_levels
 * @property string buy_groups
 * @property int once_buy_limit
 * @property int total_buy_limit
 * @property int day_buy_limit
 * @property Carbon time_begin_limit
 * @property Carbon time_end_limit
 * @property int enable_time_limit
 * @property int week_buy_limit
 * @property int month_buy_limit
 * @property Goods goods

 */
class Privilege extends BaseModel
{
    public $table = 'yz_goods_privilege';

    public $attributes = [
        'show_levels' => '',
        'show_groups' => '',
        'buy_levels' => '',
        'buy_groups' => '',
        'once_buy_limit' => 0,
        'total_buy_limit' => 0,
        'day_buy_limit' => 0,
        'week_buy_limit' => 0,
        'month_buy_limit' => 0,
        'time_begin_limit' => 0,
        'time_end_limit' => 0,
        'enable_time_limit' => 0

    ];
    /**
     *  不可填充字段.
     *
     * @var array
     */
    protected $guarded = ['created_at', 'updated_at','time_begin_limit','time_end_limit'];


    /**
     * 获取商品权限信息
     * @param $goodsId
     * @return self
     */
    public static function getGoodsPrivilegeInfo($goodsId)
    {
        $goodsPrivilegeInfo = self::where('goods_id', $goodsId)
            ->first();
        return $goodsPrivilegeInfo;
    }


    /**
     * 自定义字段名
     * 可使用
     * @return array
     */
    public function atributeNames()
    {
        return [
            'show_levels' => '会员浏览等级',
            'show_groups' => '会员浏览分组',
            'buy_levels' => '会员购买等级',
            'buy_groups' => '会员购买分组',
            'once_buy_limit' => '单次购买限制',
            'total_buy_limit' => '总购买限制',
            'day_buy_limit' => '每天购买限制',
            'week_buy_limit' => '每周购买限制',
            'month_buy_limit' => '每月购买限制',
            'time_begin_limit' => '限时起始时间',
            'time_end_limit' => '限时结束时间',
        ];
    }


    public function rules()
    {
        return [
            'show_levels' => '',
            'show_groups' => '',
            'buy_levels' => '',
            'buy_groups' => '',
            'once_buy_limit' => 'numeric',
            'total_buy_limit' => 'numeric',
            'day_buy_limit' => 'numeric',
            'week_buy_limit' => 'numeric',
            'month_buy_limit' => 'numeric',
            'time_begin_limit' => '',
            'time_end_limit' => '',
        ];
    }
    protected $casts = [
        'time_begin_limit' => 'datetime',
        'time_end_limit' => 'datetime',
    ];

    /**
     * @param Member $member
     * @param $num
     * @throws AppException
     */
    public function validate(Member $member,$num)
    {
        $this->validateTimeLimit();
        $this->validateOneBuyLimit($num);
        $this->validateDayBuyLimit($member,$num);
        $this->validateWeekBuyLimit($member,$num);
        $this->validateMonthBuyLimit($member,$num);
        $this->validateTotalBuyLimit($member,$num);
        $this->validateMemberLevelLimit($member);
        $this->validateMemberGroupLimit($member);
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }

    /**
     * 限时购
     * @throws AppException
     */
    public function validateTimeLimit()
    {
        if ($this->enable_time_limit) {
            if (Carbon::now()->lessThan($this->time_begin_limit)) {
                throw new AppException('商品(' . $this->goods->title . ')将于' . $this->time_begin_limit->toDateTimeString() . '开启限时购买');
            }
            if (Carbon::now()->greaterThanOrEqualTo($this->time_end_limit)) {
                throw new AppException('商品(' . $this->goods->title . ')该商品已于' . $this->time_end_limit->toDateTimeString() . '结束限时购买');
            }
        }
    }

    /**
     * 用户单次购买限制
     * @param $num
     * @throws AppException
     */
    public function validateOneBuyLimit($num = 1)
    {
        if ($this->once_buy_limit > 0) {
            if ($num > $this->once_buy_limit)
                throw new AppException('商品(' . $this->goods->title . ')单次最多可购买' . $this->once_buy_limit . '件');
        }
    }

    /**
     * 用户每日购买限制
     * @param Member $member
     * @param int $num
     * @throws AppException
     */
    public function validateDayBuyLimit(Member $member,$num = 1)
    {
        if ($this->day_buy_limit > 0) {
            $start_time = Carbon::today()->timestamp;
            $end_time = Carbon::now()->timestamp;
            $rang = [$start_time,$end_time];
            $history_num = $member
                ->orderGoods()
                ->where('goods_id', $this->goods_id)
                ->whereBetween('created_at',$rang)
                ->sum('total');
            if ($history_num + $num > $this->day_buy_limit)
                throw new AppException('您今天已购买' . $history_num . '件商品(' . $this->goods->title . '),该商品每天最多可购买' . $this->day_buy_limit . '件');
        }
    }

    /**
     * 用户每周购买限制
     * @param Member $member
     * @param int $num
     * @throws AppException
     */
    public function validateWeekBuyLimit(Member $member,$num = 1)
    {
        if ($this->week_buy_limit > 0) {
            $start_time = Carbon::now()->startOfWeek()->timestamp;
            $end_time = Carbon::now()->timestamp;
            $rang = [$start_time,$end_time];
            $history_num = $member
                ->orderGoods()
                ->where('goods_id', $this->goods_id)
                ->whereBetween('created_at',$rang)
                ->sum('total');
            if ($history_num + $num > $this->week_buy_limit)
                throw new AppException('您这周已购买' . $history_num . '件商品(' . $this->goods->title . '),该商品每周最多可购买' . $this->week_buy_limit . '件');
        }
    }

    /**
     * 用户每月购买限制
     * @param Member $member
     * @param int $num
     * @throws AppException
     */
    public function validateMonthBuyLimit(Member $member,$num = 1)
    {
        if ($this->month_buy_limit > 0) {
            $start_time = Carbon::now()->startOfMonth()->timestamp;
            $end_time = Carbon::now()->timestamp;
            $range = [$start_time,$end_time];

            // 购买限制不查询关闭的订单
            $orderIds = Order::select(['id', 'uid', 'status', 'created_at'])
                ->where('uid', $member->uid)
                ->where('status', '!=' ,Order::CLOSE)
                ->whereBetween('created_at',$range)
                ->pluck('id');

            $history_num = $member
                ->orderGoods()
                ->where('goods_id', $this->goods_id)
                ->whereBetween('created_at',$range)
                ->whereIn('order_id', $orderIds)
                ->sum('total');
            if ($history_num + $num > $this->month_buy_limit)
                throw new AppException('您这个月已购买' . $history_num . '件商品(' . $this->goods->title . '),该商品每月最多可购买' . $this->month_buy_limit . '件');
        }
    }

    /**
     * 用户购买总数限制
     * @param Member $member
     * @param int $num
     * @throws AppException
     */
    public function validateTotalBuyLimit(Member $member,$num = 1)
    {
        if ($this->total_buy_limit > 0) {
            $history_num = $member->orderGoods()->where('goods_id', $this->goods_id)->sum('total');
            if ($history_num + $num > $this->total_buy_limit)
                throw new AppException('您已购买' . $history_num . '件商品(' . $this->goods->title . '),最多可购买' . $this->total_buy_limit . '件');
        }
    }

    /**
     * 用户等级限制
     * @param Member $member
     * @throws AppException
     */
    public function validateMemberLevelLimit(Member $member)
    {

        if (empty($this->buy_levels) && $this->buy_levels !== '0') {
            return;
        }

        $buy_levels = explode(',', $this->buy_levels);

        if ($this->buy_levels !== '0') {
            $level_names = MemberLevel::select(DB::raw('group_concat(level_name) as level_name'))->whereIn('id', $buy_levels)->value('level_name');
            if (empty($level_names)) {
                return;
            }
        }
        if (!in_array($member->yzMember->level_id, $buy_levels)) {
            $ordinaryMember = in_array('0', $buy_levels)? '普通会员 ':'';

            throw new AppException('商品(' . $this->goods->title . ')仅限' . $ordinaryMember.$level_names . '购买');
        }
    }

    /**
     * 用户组限购
     * @param Member $member
     * @throws AppException
     */
    public function validateMemberGroupLimit(Member $member)
    {
        if (empty($this->buy_groups)) {
            return;
        }
        $buy_groups = explode(',', $this->buy_groups);
        $group_names = MemberGroup::select(DB::raw('group_concat(group_name) as level_name'))->whereIn('id', $buy_groups)->value('level_name');
        if (empty($group_names)) {
            return;
        }
        if (!in_array($member->yzMember->group_id, $buy_groups)) {
            throw new AppException('(' . $this->goods->title . ')该商品仅限[' . $group_names . ']购买');
        }
    }

    /**
     * 用户等級限制浏览
     * @param $goodsModel
     * @param $member
     * @throws AppException
     */
    public static function validatePrivilegeLevel($goodsModel, $member)
    {
        if (empty($goodsModel->hasOnePrivilege->show_levels) && $goodsModel->hasOnePrivilege->show_levels !== '0') {
            return;
        }
        $show_levels = explode(',', $goodsModel->hasOnePrivilege->show_levels);

        $level_names = MemberLevel::select(DB::raw('group_concat(level_name) as level_name'))
            ->whereIn('id', $show_levels)
            ->value('level_name');
        if (empty($level_names)) {
            return;
        }

        if (!in_array($member->level_id, $show_levels)) {
            $ordinaryMember = in_array('0', $show_levels) ? '普通会员 ' : '';

            throw new AppException('商品(' . $goodsModel->title . ')仅限' . $ordinaryMember . $level_names . '浏览');
        }
    }

    /**
     * 用户组限制浏览
     * @param
     * @throws AppException
     */
    public static function validatePrivilegeGroup($goodsModel, $member)
    {
        if (empty($goodsModel->hasOnePrivilege->show_groups)) {
            return;
        }
        $show_groups = explode(',', $goodsModel->hasOnePrivilege->show_groups);
        $group_names = MemberGroup::select(DB::raw('group_concat(group_name) as group_name'))->whereIn('id', $show_groups)->value('group_name');
        if (empty($group_names)) {
            return;
        }
        if (!in_array($member->group_id, $show_groups)) {
            throw new AppException('(' . $goodsModel->title . ')该商品仅限[' . $group_names . ']浏览');
        }
    }

}