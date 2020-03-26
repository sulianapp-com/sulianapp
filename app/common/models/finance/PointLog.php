<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/10
 * Time: 下午5:47
 */

namespace app\common\models\finance;


use app\backend\modules\member\models\Member;
use app\common\models\BaseModel;
use app\common\observers\point\PointChangeObserver;
use app\common\services\finance\PointService;
use Yunshop\Froze\Common\Services\SetService;

class PointLog extends BaseModel
{
    public $table = 'yz_point_log';
    protected $guarded = [''];
    //搜索
    protected $search_fields = ['id'];
    protected $appends = ['mode_name'];

    public static function boot()
    {
        parent::boot();
        self::observe(PointChangeObserver::class);
    }

    public static function getPointLogList($search)
    {
        $list = PointLog::lists($search);
        return $list;
    }

    public function scopeLists($query, $search)
    {
        $query->search($search);
        $builder = $query->with([
            'hasOneMember'
        ]);
        return $builder;
    }

    public function hasOneMember()
    {
        return $this->hasOne(Member::class, 'uid', 'member_id');
    }

    public function scopeSearch($query, $search)
    {
        $query->uniacid();
        $query->orderBy('id', 'desc');
        if ($search['realname'] || $search['level_id'] || $search['group_id']) {
            $query = $query->whereHas('hasOneMember', function($member)use($search) {
                if ($search['realname']) {
                    $member = $member->select('uid', 'nickname','realname','mobile','avatar')
                        ->where('realname', 'like', '%' . $search['realname'] . '%')
                        ->orWhere('mobile', 'like', '%' . $search['realname'] . '%')
                        ->orWhere('nickname', 'like', '%' . $search['realname'] . '%');
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

    public function getModeNameAttribute()
    {
        $mode_attribute = '';
        switch ($this->point_mode) {
            case (1):
                $mode_attribute = PointService::POINT_MODE_GOODS_ATTACHED;
                break;
            case (2):
                $mode_attribute = PointService::POINT_MODE_ORDER_ATTACHED;
                break;
            case (3):
                $mode_attribute = PointService::POINT_MODE_POSTER_ATTACHED;
                break;
            case (4):
                $mode_attribute = PointService::POINT_MODE_ARTICLE_ATTACHED;
                break;
            case (5):
                $mode_attribute = PointService::POINT_MODE_ADMIN_ATTACHED;
                break;
            case (6):
                $mode_attribute = PointService::POINT_MODE_BY_ATTACHED;
                break;
            case (7):
                $mode_attribute = PointService::POINT_MODE_TEAM_ATTACHED;
                break;
            case (8):
                $mode_attribute = PointService::POINT_MODE_LIVE_ATTACHED;
                break;
            case (9):
                $mode_attribute = PointService::POINT_MODE_CASHIER_ATTACHED;
                break;
            case (13):
                $mode_attribute = PointService::POINT_MODE_TRANSFER_ATTACHED;
                break;
            case (14):
                $mode_attribute = PointService::POINT_MODE_RECIPIENT_ATTACHED;
                break;
            case (15):
                $mode_attribute = PointService::POINT_MODE_ROLLBACK_ATTACHED;
                break;
            case (16):
                $mode_attribute = PointService::POINT_MODE_COUPON_DEDUCTION_AWARD_ATTACHED;
                break;
            case (17):
                $mode_attribute = PointService::POINT_MODE_TASK_REWARD_ATTACHED;
                break;
            case (18):
                if (app('plugins')->isEnabled('love')) {
                    $mode_attribute = '转入'.\Yunshop\Love\Common\Services\SetService::getLoveName();
                } else {
                    $mode_attribute = PointService::POINT_MODE_TRANSFER_LOVE_ATTACHED;
                }
                break;
            case (19):
                if (app('plugins')->isEnabled('sign')) {
                    $mode_attribute = trans('Yunshop\Sign::sign.plugin_name') . '奖励';
                } else {
                    $mode_attribute = PointService::POINT_MODE_SIGN_REWARD_ATTACHED;
                }
                break;
            case (20):
                $mode_attribute = PointService::POINT_MODE_COURIER_REWARD_ATTACHED;
                break;
            case (21):
                if (app('plugins')->isEnabled('froze')) {
                    $froze_name = SetService::getFrozeName();
                    $mode_attribute = $froze_name . '奖励';
                } else {
                    $mode_attribute = PointService::POINT_MODE_FROZE_AWARD_ATTACHED;
                }
                break;
            case (23):
                $mode_attribute = PointService::POINT_MODE_CREATE_ACTIVITY_ATTACHED;
                break;
            case (24):
                $mode_attribute = PointService::POINT_MODE_ACTIVITY_OVERDUE_ATTACHED;
                break;
            case (25):
                $mode_attribute = PointService::POINT_MODE_RECEIVE_ACTIVITY_ATTACHED;
                break;
            case (26):
                $mode_attribute = PointService::POINT_MODE_RECEIVE_OVERDUE_ATTACHED;
                break;
            case (27):
                $mode_attribute = PointService::POINT_MODE_COMMISSION_TRANSFER_ATTACHED;
                break;
            case (28):
                $mode_attribute = PointService::POINT_MODE_HOTEL_CASHIER_ATTACHED;
                break;
            case (29):
                $mode_attribute = PointService::POINT_MODE_EXCEL_RECHARGE_ATTACHED;
                break;
            case (92):
                $mode_attribute = PointService::POINT_MODE_RECHARGE_CODE_ATTACHED;
                break;
            case (93):
                $mode_attribute = PointService::POINT_MODE_STORE_ATTACHED;
                break;
            case (94):
                $mode_attribute = PointService::POINT_MODE_HOTEL_ATTACHED;
                break;
            case (22):
                $mode_attribute = PointService::POINT_MODE_COMMUNITY_REWARD_ATTACHED;
                break;
            case (30):
                $mode_attribute = PointService::POINT_MODE_CARD_VISIT_REWARD_ATTACHED;
                break;
            case (31):
                $mode_attribute = PointService::POINT_MODE_CARD_REGISTER_REWARD_ATTACHED;
                break;
            case (32):
                $mode_attribute = PointService::POINT_MODE_PRESENTATION_ATTACHED;
                break;
            case (33):
                if(app('plugins')->isEnabled('love')){
                    $mode_attribute = \Yunshop\Love\Common\Services\SetService::getLoveName() ? \Yunshop\Love\Common\Services\SetService::getLoveName().'提现扣除' : PointService::POINT_MODE_LOVE_WITHDRAWAL_DEDUCTION_ATTACHED;
                }else {
                    $mode_attribute = PointService::POINT_MODE_LOVE_WITHDRAWAL_DEDUCTION_ATTACHED;
                }
                break;
            case (34):
                $mode_attribute = PointService::POINT_MODE_FIGHT_GROUPS_TEAM_SUCCESS_ATTACHED;
                break;
            case (35):
                $mode_attribute = PointService::POINT_MODE_DRAW_CHARGE_GRT_ATTACHED;
                break;
            case (36):
                $mode_attribute = PointService::POINT_MODE_DRAW_CHARGE_DEDUCTION_ATTACHED;
                break;
            case (37):
                $mode_attribute = PointService::POINT_MODE_DRAW_REWARD_GRT_ATTACHED;
                break;
            case (38):
                $mode_attribute = PointService::POINT_MODE_CONVERT_ATTACHED;
                break;
            case (40):
                $mode_attribute = PointService::POINT_MODE_CONSUMPTION_POINTS_ATTACHED;
                break;
        }
        return $mode_attribute;
    }
}