<?php


namespace app\frontend\modules\member\services;


use app\common\models\MemberShopInfo;
use app\frontend\models\Member;
use app\frontend\modules\member\models\MemberModel;
use app\backend\modules\charts\modules\phone\models\PhoneAttribution;
use app\backend\modules\charts\modules\phone\services\PhoneAttributionService;
use app\common\helpers\Url;
use app\common\models\MemberGroup;
use app\common\services\Session;
use app\frontend\modules\member\models\MemberWechatModel;
use app\frontend\modules\member\models\SubMemberModel;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class SmsCodeService extends MemberService
{
    private $uniacid = 0;

    /**
     * @return array
     */
    public function login()
    {
        $this->uniacid  = \YunShop::app()->uniacid;
        $data = request()->input();

        if (\Request::isMethod('post')) {
            $this->validate($data);
            //检测验证码
            $checkCode = self::checkCode();
            if ($checkCode['status'] != 1) {
                return show_json(6, $checkCode['json']);
            }
            $memberInfo = MemberModel::checkMobile($this->uniacid, $data['mobile']);
            if (empty($memberInfo)) {
                $memberInfo = $this->register($data);
            }

            if(!empty($memberInfo)){
                $memberInfo = $memberInfo->toArray();
                //生成分销关系链
                Member::createRealtion($memberInfo['uid']);
                $this->save($memberInfo, $this->uniacid);
                $yz_member = MemberShopInfo::getMemberShopInfo($memberInfo['uid']);
                if (!empty($yz_member)) {
                    $yz_member = $yz_member->toArray();
                    $data = MemberModel::userData($memberInfo, $yz_member);
                } else {
                    $data = $memberInfo;
                }
                return show_json(1, $data);
            } else {
                return show_json(6, "手机号或验证码错误");
            }
        } else {
            return show_json(6,"手机号或验证码错误");
        }
    }

    public static function validate($data)
    {
        $data = array(
            'mobile' => $data['mobile'],
            'password' => $data['code'],
        );
        $rules = array(
            'mobile' => 'regex:/^1\d{10}$/',
            'code' => 'required|min:4|regex:/^[A-Za-z0-9@!#\$%\^&\*]+$/',
        );
        $message = array(
            'regex'    => ':attribute 格式错误',
            'required' => ':attribute 不能为空',
            'min' => ':attribute 最少4位'
        );
        $attributes = array(
            "mobile" => '手机号',
            'code' => '短信验证码',
        );

        $validate = \Validator::make($data,$rules,$message,$attributes);
        if ($validate->fails()) {
            $warnings = $validate->messages();
            $show_warning = $warnings->first();

            return show_json('0', $show_warning);
        } else {
            return show_json('1');
        }
    }

    //注册
    public function register($data)
    {
        $array = array();
        //获取分组
        $array['default_groupid']= MemberGroup::getDefaultGroupId($this->uniacid)->first();
        \Log::info('default_groupid', $array['default_groupid']);
        $array['member_set'] = \Setting::get('shop.member');
        \Log::info('member_set', $array['member_set']);
        if (isset($array['member_set']) && $array['member_set']['headimg']) {
            $array['avatar'] = replace_yunshop(tomedia($array['member_set']['headimg']));
        } else {
            $array['avatar'] = Url::shopUrl('static/images/photo-mr.jpg');
        }
        \Log::info('avatar', $array['avatar']);
        $array['data'] = array(
            'uniacid' => $this->uniacid,
            'mobile' => $data['mobile'],
            'groupid' => $array['default_groupid']->id ? $array['default_groupid']->id : 0,
            'createtime' => $_SERVER['REQUEST_TIME'],
            'nickname' => $data['mobile'],
            'avatar' => $array['avatar'],
            'gender' => 0,
            'residecity' => '',
        );
        $array['data']['salt'] = Str::random(8);
        \Log::info('salt', $array['data']['salt']);

        $array['data']['password'] = md5('123456' . $data['salt']);
        //dd($array['data']);
        $array['memberModel'] = MemberModel::create($array['data']);
        // dd($array['memberModel']);
        //dd($array['memberModel']);
        $array['member_id'] =  $array['memberModel']->uid;
        //手机归属地查询插入
        $array['phoneData'] = file_get_contents((new PhoneAttributionService())->getPhoneApi($data['mobile']));
        $array['phoneArray'] = json_decode($array['phoneData']);
        $array['phone']['uid'] = $array['member_id'];
        $array['phone']['uniacid'] = $this->uniacid;
        $array['phone']['province'] = $array['phoneArray']->data->province;
        $array['phone']['city'] = $array['phoneArray']->data->city;
        $array['phone']['sp'] = $array['phoneArray']->data->sp;
        $phoneModel = new PhoneAttribution();
        $phoneModel->updateOrCreate(['uid' => $data['mobile']], $array['phone']);
        //添加yz_member表
        $array['default_sub_group_id'] = MemberGroup::getDefaultGroupId()->first();

        if (!empty($array['default_sub_group_id'])) {
            $array['default_subgroup_id'] = $array['default_sub_group_id']->id;
        } else {
            $array['default_subgroup_id'] = 0;
        }
        $array['sub_data'] = array(
            'member_id' => $array['member_id'],
            'uniacid' => $this->uniacid,
            'group_id' => $array['default_subgroup_id'],
            'level_id' => 0,
            'invite_code' => \app\frontend\modules\member\models\MemberModel::generateInviteCode(),
        );
        SubMemberModel::insertData($array['sub_data']);
        //生成分销关系链
        Member::createRealtion($array['member_id']);
        $member = MemberModel::checkMobile($this->uniacid, $data['mobile']);
        if ($data['type'] == 7) {
            MemberWechatModel::insertData(array(
                'uniacid' => $this->uniacid,
                'member_id' => $array['member_info']['uid'],
                'openid' => $array['member_info']['mobile'],
                'nickname' => $array['member_info']['nickname'],
                'gender' => $array['member_info']['gender'],
                'avatar' => $array['member_info']['avatar'],
                'province' => $array['member_info']['resideprovince'],
                'city' => $array['member_info']['residecity'],
                'country' => $array['member_info']['nationality'],
                'uuid' => $data['uuid']
            ));
        }
        //dd($array);
        return $member;
    }

    /**
     * 验证登录状态
     *
     * @return bool
     */
    public function checkLogged()
    {
        return MemberService::isLogged();
    }
}