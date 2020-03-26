<?php

namespace app\common\models;

use app\backend\models\BackendModel;
use app\backend\modules\member\models\MemberUnique;
use app\common\events\member\BecomeAgent;

use app\common\events\member\PluginCreateRelationEvent;
use app\common\exceptions\AppException;
use app\common\exceptions\MemberNotLoginException;
use app\common\models\member\MemberChildren;
use app\common\models\member\MemberDel;
use app\common\models\member\MemberParent;
use app\common\services\PluginManager;
use app\common\modules\memberCart\MemberCartCollection;
use app\common\services\popularize\PortType;
use app\framework\Database\Eloquent\Collection;
use app\frontend\modules\member\models\MemberModel;
use app\frontend\modules\member\models\MemberWechatModel;
use app\frontend\repositories\MemberAddressRepository;
use Carbon\Carbon;
use Yunshop\AreaDividend\models\AreaDividendAgent;
use Yunshop\Commission\models\Agents;
use Yunshop\Gold\frontend\services\MemberCenterService;
use Yunshop\Love\Common\Models\MemberLove;
use Yunshop\Love\Common\Services\SetService;
use Yunshop\Merchant\common\models\Merchant;
use Yunshop\Micro\common\models\MicroShop;
use Yunshop\Micro\common\services\MicroShop\GetButtonService;
use Yunshop\StoreCashier\common\models\Store;
use Yunshop\Supplier\admin\models\Supplier;
use Yunshop\Supplier\common\services\VerifyButton;
use Yunshop\TeamDividend\models\TeamDividendAgencyModel;
use app\common\models\member\MemberInvitationCodeLog;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 21/02/2017
 * Time: 12:58
 */

/**
 * Class Member
 * @package app\common\models
 * @property int uid
 * @property int uniacid
 * @property string mobile
 * @property string email
 * @property string password
 * @property string salt
 * @property int groupid
 * @property float credit1
 * @property float credit2
 * @property float credit3
 * @property float credit4
 * @property float credit5
 * @property float credit6
 * @property Carbon createtime
 * @property string realname
 * @property string nickname
 * @property string avatar
 * @property string qq
 * @property int vip
 * @property int gender
 * @property int birthyear
 * @property int birthmonth
 * @property int birthday
 * @property string constellation
 * @property string zodiac
 * @property string telephone
 * @property string idcard
 * @property string studentid
 * @property string grade
 * @property string address
 * @property string zipcode
 * @property string nationality
 * @property string resideprovince
 * @property string residecity
 * @property string residedist
 * @property string graduateschool
 * @property string company
 * @property string education
 * @property string occupation
 * @property string position
 * @property string revenue
 * @property string affectivestatus
 * @property string lookingfor
 * @property string bloodtype
 * @property string height
 * @property string weight
 * @property string alipay
 * @property string msn
 * @property string taobao
 * @property string site
 * @property string bio
 * @property string interest
 * @property string pay_password
 * @property Collection memberCarts
 * @property McMappingFans hasOneFans
 * @property MemberMiniAppModel hasOneMiniApp
 * @property \app\backend\modules\member\models\MemberShopInfo yzMember
 * @property MemberDel hasOneDel
 */
class Member extends BackendModel
{
    static $current;

    protected $connection = 'mysql';

    public $table = 'mc_members';

    public $timestamps = false;


    protected $guarded = ['credit1', 'credit2', 'credit3', 'credit4', 'credit5'];

    protected $fillable = ['uniacid', 'mobile', 'groupid', 'createtime', 'nickname', 'avatar', 'gender', 'salt', 'password'];

    protected $attributes = ['bio' => '', 'resideprovince' => '', 'residecity' => '', 'nationality' => '', 'interest' => '', 'mobile' => '', 'email' => '', 'credit1' => 0, 'credit2' => 0, 'credit3' => 0, 'credit4' => 0, 'credit5' => 0, 'credit6' => 0, 'realname' => '', 'qq' => '', 'vip' => 0, 'birthyear' => 0, 'birthmonth' => 0, 'birthday' => 0, 'constellation' => '', 'zodiac' => '', 'telephone' => '', 'idcard' => '', 'studentid' => '', 'grade' => '', 'address' => '', 'zipcode' => '', 'residedist' => '', 'graduateschool' => '', 'company' => '', 'education' => '', 'occupation' => '', 'position' => '', 'revenue' => '', 'affectivestatus' => '', 'lookingfor' => '', 'bloodtype' => '', 'height' => '', 'weight' => '', 'alipay' => '', 'msn' => '', 'taobao' => '', 'site' => ''];

    const INVALID_OPENID = 0;

    protected $search_fields = ['mobile', 'uid', 'nickname', 'realname'];

    protected $primaryKey = 'uid';
    protected $appends = ['avatar_image', 'username'];

    protected $hidden = ['password', 'salt'];

    public function bankCard()
    {
        return $this->hasOne('app\common\models\member\BankCard', 'member_id', 'uid');
    }

    /**
     * @return \app\frontend\models\Member
     * @throws AppException
     */
    public static function current()
    {
        if (!isset(static::$current)) {
            static::$current = self::find(\YunShop::app()->getMemberId());
            if (!static::$current) {
                return new Member();
            }
        }
        return static::$current;
    }

    public function pointLove()
    {
        return $this->hasOne('app\common\models\finance\PointLoveSet', 'member_id', 'uid');
    }

    //关联会员删除表 yz_member_del_log
    public function hasOneDel()
    {
        return $this->hasOne('app\common\models\member\MemberDel', 'member_id', 'uid');
    }


    public function defaultAddress()
    {
        return $this->hasOne(app(MemberAddressRepository::class)->model(), 'uid', 'uid')->where('isdefault', 1);
    }

    /**
     * 主从表1:1
     *
     * @return mixed
     */
    public function yzMember()
    {
        return $this->hasOne('app\backend\modules\member\models\MemberShopInfo', 'member_id', 'uid');
    }

    /**
     * 会员－粉丝1:1关系
     *
     * @return mixed
     */
    public function hasOneFans()
    {
        return $this->hasOne('app\common\models\McMappingFans', 'uid', 'uid');
    }

    public function hasOneMiniApp()
    {
        return $this->hasOne(MemberMiniAppModel::class, 'member_id', 'uid');
    }


    /**
     * 会员－订单1:1关系 todo 会员和订单不是一对多关系吗?
     *
     * @return mixed
     */
    public function hasOneOrder()
    {
        return $this->hasOne('app\common\models\Order', 'uid', 'uid');
    }

    /**
     * 会员－会员优惠券1:多关系
     *
     * @return mixed
     */
    public function hasManyMemberCoupon()
    {
        return $this->hasOne(MemberCoupon::class, 'uid', 'uid');
    }

    /**
     * 公众号会员
     *
     * @return mixed
     */

    public function getMemberId($memberIds)
    {
        return self::select(['uid'])
            ->uniacid()
            ->whereIn('uid', $memberIds)->get()->map(function ($value) {
                return $value;
            })->toArray();
    }

    /**
     * 角色
     *
     * 会员-分销商
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneAgent()
    {
        return $this->hasOne(Agents::class, 'member_id', 'uid');
    }

    /**
     * 角色
     *
     * 会员-经销商
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneTeamDividend()
    {
        return $this->hasOne(TeamDividendAgencyModel::class, 'uid', 'uid');
    }

    /**
     * 角色
     *
     * 会员-区域代理
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneAreaDividend()
    {
        return $this->hasOne(AreaDividendAgent::class, 'member_id', 'uid');
    }

    /**
     * 角色
     *
     * 会员-招商员
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneMerchant()
    {
        return $this->hasOne(Merchant::class, 'member_id', 'uid');
    }

    /**
     * 角色
     *
     * 会员-招商中心
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneMerchantCenter()
    {
        return $this->hasOne(Merchant::class, 'member_id', 'uid');
    }

    /**
     * 角色
     *
     * 会员-微店店主
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneMicro()
    {
        return $this->hasOne(MicroShop::class, 'member_id', 'uid');
    }

    /**
     * 角色
     *
     * 会员-供应商
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneSupplier()
    {
        return $this->hasOne(Supplier::class, 'member_id', 'uid');
    }

    /**
     * 子会员
     *
     * 会员-子会员
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneMemberChildren()
    {
        return $this->hasOne(MemberChildren::class, 'member_id', 'uid');
    }

    public function hasOneMemberUnique()
    {
        return $this->hasOne(MemberUnique::class, 'member_id', 'uid');
    }

    public function hasOneMemberLove()
    {
        return $this->hasOne(MemberLove::class, 'member_id', 'uid');
    }

    public function scopeOfUid($query, $uid)
    {
        return $query->where('uid', $uid);
    }

    public function scopeSearchYzMember($query, $search)
    {
        return $query->whereHas('yzMember', function ($query) use ($search) {
            return $query->search($search);
        });
    }


    public function scopeSearch($query, $search)
    {
        if ($search['member_id']) {
            $query->ofUid($search['member_id']);
        }
        if ($search['realname']) {
            $query->searchLike($search['realname']);
        }
        if ($search['member_level'] || $search['member_group']) {
            $query->searchYzMember($search);
        }
        return $query;
    }

    /**
     * 获取用户信息
     *
     * @param $member_id
     * @return mixed
     */
    public static function getUserInfos($member_id)
    {
        return self::select(['*'])
            ->uniacid()
            ->where('uid', $member_id)
            ->with([
                'yzMember' => function ($query) {
                    return $query->select(['*'])->where('is_black', 0)
                        ->with([
                            'group' => function ($query1) {
                                return $query1->select(['id', 'group_name']);
                            },
                            'level' => function ($query2) {
                                return $query2->select(['id', 'level_name']);
                            },
                            'agent' => function ($query3) {
                                return $query3->select(['uid', 'avatar', 'nickname']);
                            }
                        ]);
                },
                'hasOneFans' => function ($query4) {
                    return $query4->select(['uid', 'openid', 'follow as followed']);
                },
                'hasOneOrder' => function ($query5) {
                    return $query5->selectRaw('uid, count(uid) as total, sum(price) as sum')
                        ->uniacid()
                        ->where('status', 3)
                        ->groupBy('uid');
                }
            ]);
    }

    /**
     * 获取该公众号下所有用户的 member ID
     *
     * @return mixed
     */
    public static function getMembersId()
    {
        return static::uniacid()
            ->select(['uid'])
            ->get();
    }

    /**
     * 通过id获取用户信息
     *
     * @param $member_id
     * @return mixed
     */
    public static function getMemberById($member_id)
    {
        return self::uniacid()
            ->where('uid', $member_id)
            ->first();
    }

    public static function getMemberByUid($member_id)
    {
        return self::uniacid()
            ->where('uid', $member_id);
    }

    /**
     * 添加评论默认名称
     * @return mixed
     */
    public static function getRandNickName()
    {
        return self::select('nickname')
            ->whereNotNull('nickname')
            ->inRandomOrder()
            ->first();
    }

    /**
     * 添加评论默认头像
     * @return mixed
     */
    public static function getRandAvatar()
    {
        return self::select('avatar')
            ->whereNotNull('avatar')
            ->inRandomOrder()
            ->first();
    }

    public static function getOpenId($member_id)
    {
        $data = self::getUserInfos($member_id)->first();
        if ($data) {
            $info = $data->toArray();

            if (!empty($info['has_one_fans'])) {
                return $info['has_one_fans']['openid'];
            } else {
                return self::INVALID_OPENID;
            }
        }
    }

    /**
     * 触发会员成为下线事件
     *
     * @param $member_id
     */
    public static function chkAgent($member_id, $mid, $mark = '', $mark_id = '')
    {
        $model = MemberShopInfo::getMemberShopInfo($member_id);

        if (1 != $model->inviter) {
            $relation = new MemberRelation();
            $relation->becomeChildAgent($mid, $model);
        }

        if ($mark_id && $mark) {
            event(new PluginCreateRelationEvent($mid, $model, $mark, $mark_id));
        }
    }

    /**
     * 定义字段名
     *
     * @return array
     */
    public function atributeNames()
    {
        return [
            'mobile' => '绑定手机号',
            'realname' => '真实姓名',
            //'avatar' => '头像',
            'telephone' => '联系手机号',
        ];
    }

    /**
     * 字段规则
     *
     * @return array
     */
    public function rules()
    {
        return [
            'mobile' => 'regex:/^1\d{10}$/',
            'realname' => 'required|between:2,10',
            //'avatar' => 'required',
            'telephone' => 'regex:/^1\d{10}$/',
        ];
    }


    /**
     * 生成分销关系链
     *
     * @param $member_id
     */
    public static function createRealtion($member_id, $upperMemberId = NULL)
    {
        $model = MemberShopInfo::getMemberShopInfo($member_id);
        \Log::info('registe_1: member_id, ', [$member_id, $model]);
        $code_mid = self::getMemberIdForInviteCode();
        \Log::info('registe_2: mid', $code_mid);

        if (!is_null($code_mid)) {

            //邀请码关系链
            $codemodel = new MemberInvitationCodeLog();
            \Log::info('registe_3_code', \YunShop::request()->invite_code);

            if (!$codemodel->where('member_id', $member_id)->where('mid', $code_mid)->first()) {
                \Log::info('add_codemodel');
                $codemodel->uniacid = \YunShop::app()->uniacid;
                \Log::info('--uniacid', \YunShop::app()->uniacid);
                $codemodel->invitation_code = trim(\YunShop::request()->invite_code);
                \Log::info('--invitation_code', \YunShop::request()->invite_code);

                $codemodel->member_id = $member_id; //使用者id
                \Log::info('--member_id', $member_id);

                $codemodel->mid = $code_mid; //邀请人id
                \Log::info('--mid', $code_mid);

                $codemodel->save();
                \Log::info('registe_4', $codemodel->save());

            } else {
                \Log::info('已存在');
            }


            file_put_contents(storage_path("logs/" . date('Y-m-d') . "_invitecode.log"), print_r($member_id . '-' . \YunShop::request()->invite_code . '-' . $code_mid . '-reg' . PHP_EOL, 1), FILE_APPEND);
        }

        $mid = !is_null($code_mid) ? $code_mid : self::getMid();
        $mid = !is_null($upperMemberId) ? $upperMemberId : $mid;

        event(new BecomeAgent($mid, $model));
    }

    public static function getMid()
    {
        $mid = \YunShop::request()->mid;

        return ($mid && ($mid != 'null' || $mid != 'undefined')) ? (int)$mid : 0;
    }

    /**
     * 申请插件
     *
     * @param array $data
     * @return array
     */
    public static function addPlugins(&$data = [])
    {
        $plugin_class = app('plugins');

        //供应商
        if ($plugin_class->isEnabled('supplier')) {
            $data['supplier'] = VerifyButton::button();
        } else {
            $data['supplier'] = '';
        }

        //微店
        if ($plugin_class->isEnabled('micro')) {
            $micro_set = \Setting::get('plugin.micro');
            if ($micro_set['is_open_miceo'] == 0) {
                $data['micro'] = '';
            } else {
                $data['micro'] = GetButtonService::verify(\YunShop::app()->getMemberId());
            }
        } else {
            $data['micro'] = '';
        }

        if ($plugin_class->isEnabled('gold')) {
            $data['gold'] = MemberCenterService::button(\YunShop::app()->getMemberId());
        } else {
            $data['gold'] = '';
        }

        //爱心值
        if ($plugin_class->isEnabled('love')) {
            $data['love'] = [
                'status' => true,
                'love_name' => SetService::getLoveName(),
            ];
        } else {
            $data['love'] = [
                'status' => false,
                'love_name' => '爱心值',
            ];
        }

        if ($plugin_class->isEnabled('froze')) {
            $data['froze'] = [
                'status' => true,
                'froze_name' => \Yunshop\Froze\Common\Services\SetService::getFrozeName(),
            ];
        } else {
            $data['froze'] = [
                'status' => false,
                'froze_name' => '冻结币',
            ];
        }

        if ($plugin_class->isEnabled('coin')) {
            $data['coin'] = [
                'status' => true,
                'coin_name' => \Yunshop\Coin\Common\Services\SetService::getCoinName(),
            ];
        } else {
            $data['coin'] = [
                'status' => false,
                'coin_name' => '华侨币',
            ];
        }

        if ($plugin_class->isEnabled('store-cashier')) {
            $store = Store::getStoreByUid(\YunShop::app()->getMemberId())->first();
            if ($store && $store->hasOneCashier->hasOneCashierGoods->is_open == 1) {
                $data['cashier'] = [
                    'button_name' => '收银台',
                    'api' => 'plugin.store-cashier.frontend.cashier.center.index'
                ];
            }
        } else {
            $data['cashier'] = '';
        }

        if ($plugin_class->isEnabled('elive')) {
            $data['elive'] = [
                'button_name' => '生活缴费',
                'status' => true
            ];
        } else {
            $data['elive'] = ['status' => false];
        }

        if ($plugin_class->isEnabled('sign')) {
            $data['sign'] = [
                'status' => true,
                'plugin_name' => trans('Yunshop\Sign::sign.plugin_name'),
            ];
        } else {
            $data['sign'] = [
                'status' => false,
                'plugin_name' => '签到',
            ];
        }

        //快递单插件开启
        if ($plugin_class->isEnabled('courier')) {
            $status = \Setting::get('courier.courier.radio') ? true : false;

            $data['courier'] = [
                'button_name' => '快递',
                'status' => $status
            ];
        } else {
            $data['courier'] = [
                'button_name' => '快递',
                'status' => false
            ];
        }


        //帮助中心插件开启控制
        if ($plugin_class->isEnabled('help-center')) {
            $status = \Setting::get('help-center.status') ? true : false;

            $data['help_center'] = [
                'button_name' => '帮助中心',
                'status' => $status
            ];
        } else {
            $data['help_center'] = [
                'button_name' => '帮助中心',
                'status' => false
            ];
        }


        //隐藏爱心值插件入口
        $love_show = PortType::popularizeShow(\YunShop::request()->type);
        if (isset($data['love']) && (!$love_show)) {
            $data['love']['status'] = false;
        }

        //配送站
        if (app('plugins')->isEnabled('delivery-station')) {
            $data['is_open_delivery_station'] = \Setting::get('plugin.delivery_station.is_open') ? 1 : 0;
        } else {
            $data['is_open_delivery_station'] = 0;
        }

        return $data;
    }

    /**
     * 推广提现
     * @return \Illuminate\Http\JsonResponse
     */
    public static function getIncomeCount()
    {
        $amount = Income::getIncomes()->where('member_id', \YunShop::app()->getMemberId())->sum('amount');

        if ($amount) {
            return number_format($amount, 2);
        }

        return number_format(0, 2);
    }

    /**
     * 会员3级关系链
     *
     * @param $uid
     * @param string $mid
     * @return bool
     */
    public static function setMemberRelation($uid, $mid = '')
    {
        $curr_arr = [];

        $model = MemberShopInfo::getMemberShopInfo($uid);

        if (empty($mid)) {
            $mid = 0;
        }

        //生成关系3级关系链
        $member_model = MemberModel::getMyAgentsParentInfo($mid)->first();

        if (!empty($member_model)) {
            \Log::debug('model-生成关系3级关系链');
            $member_data = $member_model->toArray();

            $relation_str = $mid;

            if (!empty($member_data['yz_member'])) {
                $count = count($member_data['yz_member'], 1);

                if ($count > 3) {
                    $relation_str .= ',' . $member_data['yz_member']['parent_id'];
                }

                if ($count > 6) {
                    $relation_str .= ',' . $member_data['yz_member']['has_one_pre_self']['parent_id'];
                }
            }
        } else {
            $relation_str = '0';
        }

        if ($relation_str != '0') {
            $curr_arr = explode(',', rtrim($relation_str, ','));
            $res_arr = array_unique($curr_arr);

            if (count($res_arr) != count($curr_arr)) {
                return false;
            }

            if (in_array($uid, $curr_arr)) {
                return false;
            }
        }

        $model->relation = $relation_str;
        $model->save();

        return $curr_arr;
    }

    public static function getOpenIdForType($member_id, $type = null)
    {
        switch ($type) {
            case 2:
                $mini_app = MemberMiniAppModel::getFansById($member_id);

                return $mini_app->openid;
                break;
            case 9:
                $mini_app = MemberWechatModel::getFansById($member_id);

                return $mini_app->openid;
                break;
            default:
                $fans = McMappingFans::getFansById($member_id);

                return $fans->openid;
        }
    }

    /**
     * 判断用户是否关注
     *
     * @return bool
     */
    public function isFollow()
    {
        return isset($this->hasOneFans) && $this->hasOneFans->follow && !empty($this->hasOneFans->openid);
    }

    public function getMemberRole($builder)
    {
        $result = $builder;

//        if (app('plugins')->isEnabled('commission')) {
//            $result = $result->with([
//                'hasOneAgent'
//            ]);
//        }
//
//        if (app('plugins')->isEnabled('team-dividend')) {
//            $result = $result->with([
//                'hasOneTeamDividend'
//            ]);
//        }
//
//        if (app('plugins')->isEnabled('area-dividend')) {
//            $result = $result->with([
//                'hasOneAreaDividend' => function ($query) {
//                    return $query->where('status', 1);
//                }
//            ]);
//        }
//
//        if (app('plugins')->isEnabled('merchant')) {
//            $result = $result->with([
//                'hasOneMerchant',
//                'hasOneMerchantCenter'
//            ]);
//        }
//
//        if (app('plugins')->isEnabled('micro')) {
//            $result = $result->with([
//                'hasOneMicro'
//            ]);
//        }
//
//        if (app('plugins')->isEnabled('supplier')) {
//            $result = $result->with([
//                'hasOneSupplier' => function ($query) {
//                    return $query->where('status', 1);
//                }
//            ]);
//        }

        return $result;
    }

    public static function getPid()
    {
        $pid = \YunShop::request()->pid;

        return ($pid && ($pid != 'null' || $pid != 'undefined')) ? (int)$pid : 0;
    }

    //快递单获取会员信息
    public static function getMemberInfo($uid)
    {
        return self::uniacid()->find($uid);
    }

    public static function deleted($uid)
    {
        self::uniacid()
            ->where('uid', $uid)
            ->delete();
    }

    public function getAvatarImageAttribute()
    {
        return $this->avatar ? yz_tomedia($this->avatar) : yz_tomedia(\Setting::get('shop.member.headimg'));
    }

    public function getUserNameAttribute()
    {
        if (substr($this->nickname, 0, strlen('=')) === '=') {
            $this->nickname = ' ' . $this->nickname;
        }
        return $this->nickname;
    }

    /**
     * 邀请码会员
     *
     * @return null
     */
    public function getMemberIdForInviteCode()
    {
        if ($invite_code = self::hasInviteCode()) {
            $ids = MemberShopInfo::getMemberIdForInviteCode($invite_code);

            if (!is_null($ids)) {
                return $ids[0];
            }
        }

        return null;
    }

    public static function hasInviteCode()
    {
        $required = intval(\Setting::get('shop.member.required'));
        $invite_code = \YunShop::request()->invite_code;
        $is_invite = self::chkInviteCode();

        $member = MemberShopInfo::where('invite_code', $invite_code)->count();

        if ($is_invite && $required && empty($invite_code)) {
            return null;
        }

        if ($is_invite && isset($invite_code) && !empty($invite_code) && !empty($member)) {
            return $invite_code;
        }

        return null;
    }

    /**
     * 购物车记录
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function memberCarts()
    {
        return $this->hasMany(MemberCart::class, 'uid', 'member_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderGoods()
    {
        return $this->hasMany(OrderGoods::class, 'uid', 'uid');
    }

    /**
     * @return MemberCartCollection|mixed
     */
    public function getMemberCartCollection()
    {
        if (!isset($this->memberCartCollection)) {
            $this->memberCartCollection = new MemberCartCollection($this->memberCarts->all());
        }
        return $this->memberCartCollection;
    }

    /**
     * 邀请码是否开启
     *
     * @return int
     */
    public static function chkInviteCode()
    {
        $is_invite = intval(\Setting::get('shop.member.is_invite'));
        $invite_page = intval(\Setting::get('shop.member.invite_page'));

//        //邀请页和邀请码都开启
//        if (1 == $invite_page && 1 == $is_invite) {
//            $is_invite = 0;
//        }

        return $is_invite;
    }
}