<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/22
 * Time: 上午11:56
 */

namespace app\frontend\modules\member\controllers;

use app\backend\modules\charts\modules\phone\models\PhoneAttribution;
use app\backend\modules\charts\modules\phone\services\PhoneAttributionService;
use app\common\components\ApiController;
use app\common\helpers\Client;
use app\common\helpers\Url;
use app\common\models\Member;
use app\common\models\member\MemberInvitationCodeLog;
use app\common\models\MemberGroup;
use app\common\models\MemberLevel;
use app\common\models\MemberShopInfo;
use app\common\services\aliyun\AliyunSMS;
use app\common\services\Session;
use app\frontend\modules\member\models\MemberModel;
use app\frontend\modules\member\models\SubMemberModel;
use app\frontend\modules\member\models\MemberWechatModel;
use app\frontend\modules\member\services\MemberPluginSmsService;
use app\frontend\modules\member\services\MemberService;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;
use iscms\Alisms\SendsmsPusher as Sms;
use app\common\exceptions\AppException;
use Mews\Captcha\Captcha;
use app\common\facades\Setting;
use app\common\services\alipay\OnekeyLogin;
use app\common\models\McMappingFans;


class RegisterController extends ApiController
{
    protected $publicController = ['Register'];
    protected $publicAction = ['index', 'sendCode', 'sendCodeV2', 'checkCode', 'sendSms', 'changePassword', 'getInviteCode', 'chkRegister', 'alySendCode'];
    protected $ignoreAction = ['index', 'sendCode', 'sendCodeV2', 'checkCode', 'sendSms', 'changePassword', 'getInviteCode', 'chkRegister', 'alySendCode'];

    public function index()
    {
        $mobile = \YunShop::request()->mobile;
        $password = \YunShop::request()->password;
        $confirm_password = \YunShop::request()->confirm_password;
        $uniacid = \YunShop::app()->uniacid;

        if ((\Request::getMethod() == 'POST')) {
            $check_code = MemberService::checkCode();

            if ($check_code['status'] != 1) {
                return $this->errorJson($check_code['json']);
            }

            $invite_code = MemberService::inviteCode();
            \Log::info('invite_code', $invite_code);

            if ($invite_code['status'] != 1) {
                return $this->errorJson($invite_code['json']);
            }

            $msg = MemberService::validate($mobile, $password, $confirm_password);

            if ($msg['status'] != 1) {
                return $this->errorJson($msg['json']);
            }

            $member_info = MemberModel::getId($uniacid, $mobile);

            \Log::info('member_info', $member_info);

            if (!empty($member_info)) {
                return $this->errorJson('该手机号已被注册');
            }

            //添加mc_members表
            $default_groupid = MemberGroup::getDefaultGroupId($uniacid)->first();
            \Log::info('default_groupid', $default_groupid);

            $member_set = \Setting::get('shop.member');
            \Log::info('member_set', $member_set);

            if (isset($member_set) && $member_set['headimg']) {
                $avatar = replace_yunshop(tomedia($member_set['headimg']));
            } else {
                $avatar = Url::shopUrl('static/images/photo-mr.jpg');
            }
            \Log::info('avatar', $avatar);

            $data = array(
                'uniacid' => $uniacid,
                'mobile' => $mobile,
                'groupid' => $default_groupid->id ? $default_groupid->id : 0,
                'createtime' => time(),
                'nickname' => $mobile,
                'avatar' => $avatar,
                'gender' => 0,
                'residecity' => '',
            );
            $data['salt'] = Str::random(8);
            \Log::info('salt', $data['salt']);

            $data['password'] = md5($password . $data['salt']);
            $memberModel = MemberModel::create($data);
            $member_id = $memberModel->uid;

            //手机归属地查询插入
            $phoneData = file_get_contents((new PhoneAttributionService())->getPhoneApi($mobile));
            $phoneArray = json_decode($phoneData);
            $phone['uid'] = $member_id;
            $phone['uniacid'] = $uniacid;
            $phone['province'] = $phoneArray->data->province;
            $phone['city'] = $phoneArray->data->city;
            $phone['sp'] = $phoneArray->data->sp;

            $phoneModel = new PhoneAttribution();
            $phoneModel->updateOrCreate(['uid' => $member_id], $phone);

            //添加yz_member表
            $default_sub_group_id = MemberGroup::getDefaultGroupId()->first();

            if (!empty($default_sub_group_id)) {
                $default_subgroup_id = $default_sub_group_id->id;
            } else {
                $default_subgroup_id = 0;
            }

            $sub_data = array(
                'member_id' => $member_id,
                'uniacid' => $uniacid,
                'group_id' => $default_subgroup_id,
                'level_id' => 0,
                'invite_code' => \app\frontend\modules\member\models\MemberModel::generateInviteCode(),
            );

            SubMemberModel::insertData($sub_data);
            //生成分销关系链
            Member::createRealtion($member_id);

            $cookieid = "__cookie_yun_shop_userid_{$uniacid}";
            Cookie::queue($cookieid, $member_id);
            Session::set('member_id', $member_id);

            $password = $data['password'];
            $member_info = MemberModel::getUserInfo($uniacid, $mobile, $password)->first();
            $yz_member = MemberShopInfo::getMemberShopInfo($member_id)->toArray();

            $data = MemberModel::userData($member_info, $yz_member);

            //app注册添加member_wechat表中数据
            $type = \YunShop::request()->type;
            if ($type == 7) {
                $uuid = \YunShop::request()->uuid;
                MemberWechatModel::insertData(array(
                    'uniacid' => $uniacid,
                    'member_id' => $member_info['uid'],
                    'openid' => $member_info['mobile'],
                    'nickname' => $member_info['nickname'],
                    'gender' => $member_info['gender'],
                    'avatar' => $member_info['avatar'],
                    'province' => $member_info['resideprovince'],
                    'city' => $member_info['residecity'],
                    'country' => $member_info['nationality'],
                    'uuid' => $uuid
                ));
            }
            return $this->successJson('', $data);
        } else {
            return $this->errorJson('手机号或密码格式错误');
        }
    }

    /**
     * 发送短信验证码
     *
     *
     */
    public function sendCode()
    {
        $mobile = \YunShop::request()->mobile;

        $reset_pwd = \YunShop::request()->reset;

        if (empty($mobile)) {
            return $this->errorJson('请填入手机号');
        }

        $info = MemberModel::getId(\YunShop::app()->uniacid, $mobile);

        if (!empty($info) && empty($reset_pwd)) {
            return $this->errorJson('该手机号已被注册！不能获取验证码');
        }
        $code = rand(1000, 9999);

        Session::set('codetime', time());
        Session::set('code', $code);
        Session::set('code_mobile', $mobile);

        //$content = "您的验证码是：". $code ."。请不要把验证码泄露给其他人。如非本人操作，可不用理会！";

        if (!MemberService::smsSendLimit(\YunShop::app()->uniacid, $mobile)) {
            return $this->errorJson('发送短信数量达到今日上限');
        } else {
            $this->sendSms($mobile, $code);
        }
    }

    public function alySendCode()
    {
        $mobile = \YunShop::request()->mobile;

        $state = \YunShop::request()->state ?: '86';

        $sms_type = \YunShop::request()->sms_type;

        if (empty($mobile)) {
            return $this->errorJson('请填入手机号');
        }

        $type = \YunShop::request()->type;
        if (empty($type)) {
            $type = Client::getType();
        }

        $code = rand(1000, 9999);
        Session::set('codetime', time());
        Session::set('code', $code);
        Session::set('code_mobile', $mobile);

        //$content = "您的验证码是：". $code ."。请不要把验证码泄露给其他人。如非本人操作，可不用理会！";
        return $this->sendSmsV2($mobile, $code, $state, 'reg', $sms_type);
    }

    public function sendCodeV2()
    {
        $mobile = \YunShop::request()->mobile;

        $reset_pwd = \YunShop::request()->reset;

        $state = \YunShop::request()->state ?: '86';

        $sms_type = \YunShop::request()->sms_type;

        if (empty($mobile)) {
            return $this->errorJson('请填入手机号');
        }

        $type = \YunShop::request()->type;
        if (empty($type)) {
            $type = Client::getType();
        }

        //微信登录绑定已存在的手机号
        if ($type == 1) {
            $memberinfo = MemberModel::getId(\YunShop::app()->uniacid, $mobile);

            if (!empty($memberinfo['uid'])) {

                $fansinfo = McMappingFans::getFansById($memberinfo['uid']);

                if ($fansinfo) {

                    return $this->errorJson('该手机号已被绑定！不能获取验证码');
                }
            }
        }

        if ((!OnekeyLogin::alipayPluginMobileState() || $type == 5) && $type != 1) {
            $info = MemberModel::getId(\YunShop::app()->uniacid, $mobile);

            if (!empty($info) && empty($reset_pwd)) {
                return $this->errorJson('该手机号已被注册！不能获取验证码');
            }
        }

        $code = rand(1000, 9999);

        Session::set('codetime', time());
        Session::set('code', $code);
        Session::set('code_mobile', $mobile);

        //$content = "您的验证码是：". $code ."。请不要把验证码泄露给其他人。如非本人操作，可不用理会！";

        if (!MemberService::smsSendLimit(\YunShop::app()->uniacid, $mobile)) {
            return $this->errorJson('发送短信数量达到今日上限');
        } else {
            return $this->sendSmsV2($mobile, $code, $state, 'reg', $sms_type);
        }
    }

    public function sendWithdrawCode()
    {
        $mobile = \YunShop::request()->mobile;
        $reset_pwd = \YunShop::request()->reset;

        if (empty($mobile)) {
            return $this->errorJson('请填入手机号');
        }
        $code = rand(1000, 9999);

        Session::set('codetime', time());
        Session::set('code', $code);
        Session::set('code_mobile', $mobile);

        //$content = "您的验证码是：". $code ."。请不要把验证码泄露给其他人。如非本人操作，可不用理会！";

        if (!MemberService::smsSendLimit(\YunShop::app()->uniacid, $mobile)) {
            return $this->errorJson('发送短信数量达到今日上限');
        } else {
           return  $this->sendSms($mobile, $code);
        }
    }


    /**
     * 发送短信
     *
     * @param $mobile
     * @param $code
     * @param string $templateType
     * @return array|mixed
     */
    public function sendSms($mobile, $code, $templateType = 'reg')
    {
        $sms = \Setting::get('shop.sms');

        //增加验证码验证
        $captcha_status = Setting::get('shop.sms.status');
        if ($captcha_status == 1) {
            if (app('captcha')->check(Input::get('captcha')) == false) {
                return $this->errorJson('验证码错误');
            }
        }

        //互亿无线
        if ($sms['type'] == 1) {
            $issendsms = MemberService::send_sms(trim($sms['account']), trim($sms['password']), $mobile, $code);

            if ($issendsms['SubmitResult']['code'] == 2) {
                MemberService::udpateSmsSendTotal(\YunShop::app()->uniacid, $mobile);
                return $this->successJson();
            } else {
                return $this->errorJson('短信设置' . $issendsms['SubmitResult']['msg'] . ',' . '请前往设置');
            }
        } elseif ($sms['type'] == 2) {
            $result = MemberService::send_sms_alidayu($sms, $templateType);

            if (count($result['params']) > 1) {
                $nparam['code'] = "{$code}";
                foreach ($result['params'] as $param) {
                    $param = trim($param);
                    $explode_param = explode("=", $param);
                    if (!empty($explode_param[0])) {
                        $nparam[$explode_param[0]] = "{$explode_param[1]}";
                    }
                }

                $content = json_encode($nparam);
            } else {
                $explode_param = explode("=", $result['params'][0]);
                $content = json_encode(array('code' => (string)$code, 'product' => $explode_param[1]));
            }

            $top_client = new \iscms\AlismsSdk\TopClient(trim($sms['appkey']), trim($sms['secret']));
            $name = trim($sms['signname']);
            $templateCode = trim($sms['templateCode']);

            config([
                'alisms.KEY' => trim($sms['appkey']),
                'alisms.SECRETKEY' => trim($sms['secret'])
            ]);

            $sms = new Sms($top_client);
            $issendsms = $sms->send($mobile, $name, $content, $templateCode);

            if (isset($issendsms->result->success)) {
                MemberService::udpateSmsSendTotal(\YunShop::app()->uniacid, $mobile);
                return $this->successJson();
            } else {
                //return $this->errorJson($issendsms->msg . '/' . $issendsms->sub_msg);
            }
        } elseif ($sms['type'] == 3) {
            $aly_sms = new AliyunSMS(trim($sms['aly_appkey']), trim($sms['aly_secret']));

            $response = $aly_sms->sendSms(
                $sms['aly_signname'], // 短信签名
                $sms['aly_templateCode'], // 短信模板编号
                $mobile, // 短信接收者
                Array(  // 短信模板中字段的值
                    "number" => $code
                )
            );

            if ($response->Code == 'OK' && $response->Message == 'OK') {
                return $this->successJson();
            } else {
                return $this->errorJson($response->Message);
            }

        } else {
            return $this->errorJson('未设置短信功能');
        }
    }

    public function sendSmsV2($mobile, $code, $state, $templateType = 'reg', $sms_type = 1)
    {
        $sms = \Setting::get('shop.sms');

        //增加验证码验证
        $captcha_status = Setting::get('shop.sms.status');
        if ($captcha_status == 1) {
            if (app('captcha')->check(Input::get('captcha')) == false) {
                return $this->errorJson('图形验证码错误');
            }
        }

        //互亿无线
        if ($sms['type'] == 1) {
            if ($state != '86') {
                $account = trim($sms['account2']);
                $password = trim($sms['password2']);
            } else {
                $account = trim($sms['account']);
                $password = trim($sms['password']);
            }
            $issendsms = MemberService::send_smsV2($account, $password, $mobile, $code, $state);

            if ($issendsms['SubmitResult']['code'] == 2) {
                MemberService::udpateSmsSendTotal(\YunShop::app()->uniacid, $mobile);
                return $this->successJson();
            } else {
                return $this->errorJson('短信设置' . $issendsms['SubmitResult']['msg'] . ',' . '请前往设置');
            }
        } elseif ($sms['type'] == 2) {
            $result = MemberService::send_sms_alidayu($sms, $templateType);

            if (count($result['params']) > 1) {
                $nparam['code'] = "{$code}";
                foreach ($result['params'] as $param) {
                    $param = trim($param);
                    $explode_param = explode("=", $param);
                    if (!empty($explode_param[0])) {
                        $nparam[$explode_param[0]] = "{$explode_param[1]}";
                    }
                }

                $content = json_encode($nparam);
            } else {
                $explode_param = explode("=", $result['params'][0]);
                $content = json_encode(array('code' => (string)$code, 'product' => $explode_param[1]));
            }

            $top_client = new \iscms\AlismsSdk\TopClient(trim($sms['appkey']), trim($sms['secret']));
            $name = trim($sms['signname']);
            $templateCode = trim($sms['templateCode']);
            $templateCodeForget = trim($sms['templateCodeForget']);
            config([
                'alisms.KEY' => trim($sms['appkey']),
                'alisms.SECRETKEY' => trim($sms['secret'])
            ]);

            $sms = new Sms($top_client);

            //$type为1是注册，else 找回密码
            if (!is_null($sms_type) && $sms_type == 2) {
                $issendsms = $sms->send($mobile, $name, $content, $templateCodeForget);
            } else {
                $issendsms = $sms->send($mobile, $name, $content, $templateCode);
            }

            if (isset($issendsms->result->success)) {
                MemberService::udpateSmsSendTotal(\YunShop::app()->uniacid, $mobile);
                return $this->successJson();
            } else {
                //return $this->errorJson($issendsms->msg . '/' . $issendsms->sub_msg);
            }
        } elseif ($sms['type'] == 3) {
            $aly_sms = new AliyunSMS(trim($sms['aly_appkey']), trim($sms['aly_secret']));

            //$type为1是注册，2是找回密码
            if (!is_null($sms_type) && $sms_type == 2) {
                $response = $aly_sms->sendSms(
                    $sms['aly_signname'], // 短信签名
                    $sms['aly_templateCodeForget'], // 找回密码短信模板编号
                    $mobile, // 短信接收者
                    Array(  // 短信模板中字段的值
                        "number" => $code
                    )
                );
            } else {
                $response = $aly_sms->sendSms(
                    $sms['aly_signname'], // 短信签名
                    $sms['aly_templateCode'], // 注册短信模板编号
                    $mobile, // 短信接收者
                    Array(  // 短信模板中字段的值
                        "number" => $code
                    )
                );
            }

            if ($response->Code == 'OK' && $response->Message == 'OK') {
                return $this->successJson();
            } else {
                return $this->errorJson($response->Message);
            }

        } elseif($sms['type'] == 4){
            event(new \app\common\events\SmsEvent($mobile,$code,$sms));
        }else {
            return $this->errorJson('未设置短信功能');
        }
    }

    /**
     * 短信验证
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkCode()
    {
        $mobile = \YunShop::request()->mobile;
        $uniacid = \YunShop::app()->uniacid;

        $check_code = MemberService::checkCode();
        $member_info = MemberModel::getId($uniacid, $mobile);

        if (empty($member_info)) {
            return $this->errorJson('手机号不存在');
        }

        if ($check_code['status'] != 1) {
            return $this->errorJson($check_code['json']);
        }

        return $this->successJson('ok');
    }

    /**
     * 修改密码
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword()
    {
        $mobile = \YunShop::request()->mobile;
        $password = \YunShop::request()->password;
        $confirm_password = \YunShop::request()->confirm_password;
        $uniacid = \YunShop::app()->uniacid;

        if ((\Request::getMethod() == 'POST')) {
            $check_code = MemberService::checkCode();

            if ($check_code['status'] != 1) {
                return $this->errorJson($check_code['json']);
            }

            $msg = MemberService::validate($mobile, $password, $confirm_password);

            if ($msg['status'] != 1) {
                return $this->errorJson($msg['json']);
            }

            $member_info = MemberModel::getId($uniacid, $mobile);

            if (empty($member_info)) {
                return $this->errorJson('该手机号不存在');
            }

            //更新密码
            $data['salt'] = Str::random(8);
            $data['password'] = md5($password . $data['salt']);

            MemberModel::updataData($member_info->uid, $data);
            $member_id = $member_info->uid;

            $password = $data['password'];
            $member_info = MemberModel::getUserInfo($uniacid, $mobile, $password)->first();
            $yz_member = MemberShopInfo::getMemberShopInfo($member_id)->toArray();

            $data = MemberModel::userData($member_info, $yz_member);

            return $this->successJson('', $data);
        } else {
            return $this->errorJson('手机号或密码格式错误');
        }
    }

    public function getInviteCode()
    {
        $close = \YunShop::request()->close;
        $required = intval(\Setting::get('shop.member.required'));
     //  $is_invite = Member::chkInviteCode();
        $is_invite = intval(\Setting::get('shop.member.is_invite'));
        $mid = \YunShop::request()->get('mid');

        if($is_invite == 1){
            $invitation_code = MemberShopInfo::select('invite_code')->where('member_id',$mid)->first();
        }
        // 国家区号是否显示
        $country_code = 0; // 默认关闭
        $sms = \Setting::get('shop.sms');
        if (isset($sms['country_code'])) {
            $country_code = $sms['country_code'];
        }

        if (isset($close) && 1 == $close) {
            $is_invite = 0;
            $required = 0;
        }

        $data = [
            'status' => $is_invite,
            'required' => $required,
            'country_code' => $country_code,
            'invitation_code'=>$invitation_code,
        ];

        return $this->successJson('ok', $data);
    }

    public function chkRegister()
    {
        $member = Setting::get('shop.member');
        $shop_reg_close = !empty($member['get_register']) ? $member['get_register'] : 0;
        $app_reg_close = 0;
        $msg = $member["Close_describe"] ?: '注册已关闭';//关闭原因
        $list = [];
        //$list['state']= $shop_reg_close;
        $list['state'] = $list['state'] = $shop_reg_close;
        if (!is_null($app_set = \Setting::get('shop_app.pay')) && 0 == $app_set['phone_oauth']) {
            $app_reg_close = 1;
        }

        if (($shop_reg_close && !Client::is_app()) || ($app_reg_close && Client::is_app())) {
            $list['reason'] = $msg;
            return $this->errorJson('失败', $list);
        }
        return $this->successJson('ok', $list);
    }
}
