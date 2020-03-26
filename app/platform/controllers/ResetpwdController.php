<?php

namespace app\platform\controllers;

use app\frontend\modules\member\services\MemberService;
use app\platform\modules\user\models\AdminUser;
use app\platform\modules\user\models\YzUserProfile;
use app\common\helpers\Cache;
use iscms\Alisms\SendsmsPusher as Sms;
use app\frontend\modules\member\models\smsSendLimitModel;
use app\platform\modules\system\models\SystemSetting;
use app\common\services\aliyun\AliyunSMS;
use Mews\Captcha\Captcha;
use Gregwar\Captcha\PhraseBuilder;
use Gregwar\Captcha\CaptchaBuilder;
use app\common\helpers\Url;

class ResetpwdController extends BaseController
{
	public function SendCode()
	{
		$mobile = request()->mobile;
        $state = \YunShop::request()->state ? : '86';

		if (empty($mobile)) {
            return $this->errorJson('请填入手机号');
        }
		
        $uid = $this->checkUserOnMobile($mobile); 
        if (!$uid) {
            return $this->errorJson('该手机号不存在');
        }

        return $this->send($mobile, $state);
	}

	public function send($mobile, $state)
    {
        $code = rand(1000, 9999);

        Cache::put($mobile.'_code', $code, 60 * 10);
        //检查次数及是否正确
        if (!MemberService::smsSendLimit(\YunShop::app()->uniacid? : 0, $mobile)) {
            return $this->errorJson('发送短信数量达到今日上限');
        } else {
            return $this->sendSmsV2($mobile, $code, $state);
        }
    }

	public function checkCode()
	{
		$mobile = request()->mobile;
		$code = request()->code;

		//检查验证码是否正确
        if (!Cache::has($mobile.'_code')) {
            return $this->errorJson('验证码已失效,请重新获取');
        }
        if ($code != Cache::get($mobile.'_code')) {
        	return $this->errorJson('验证码错误');
        }
        return $this->successJson('验证成功');
	}

	public function detail()
	{
		$setting = SystemSetting::settingLoad('sms', 'system_sms');
		
		if (!$setting) {
			return $this->errorJson('暂无数据');
		}
		return $this->successJson('获取成功', $setting);
	}

	public function getCaptcha()
	{
		$setting = SystemSetting::settingLoad('sms');

		if ($setting['status'] != 1) {
			return $this->errorJson('请开启图形验证码验证');
		}
		$phrase = new PhraseBuilder();
        $code = $phrase->build(4);
        $builder = new CaptchaBuilder($code, $phrase);

        $builder->setBackgroundColor(150, 150, 150);
        $builder->setMaxAngle(25);
        $builder->setMaxBehindLines(0);
        $builder->setMaxFrontLines(0);

        $builder->build($width = 100, $height = 40, $font = null);
        $phrase = $builder->getPhrase();

        \Session::flash('code', $phrase);

        // header('Cache-Control: no-cache, must-revalidate');
        header('Content-Type: image/jpeg');
        $builder->output();		
	}

	public function changePwd()
	{
		$pwd = request()->pwd;
		$mobile = request()->mobile;
        $confirm_password = \YunShop::request()->confirm_password;
        
        $msg = $this->validate($mobile, $pwd, $confirm_password);
        if ($msg != 1) {
            return $this->errorJson($msg['json']);
        }

		$uid = $this->checkUserOnMobile($mobile); 
		if (!$uid) {
			return $this->errorJson('该手机号不存在');
		}

		$res = $this->modify($pwd, $uid);

        if ($res) {
            return $this->successJson('密码修改成功');
        }
        return $this->errorJson('修改密码失败');
	}

	private function checkUserOnMobile($mobile) 
	{
		$member_info = YzUserProfile::where('mobile', $mobile)->first();

        if ($member_info) {
        	return $member_info['uid'];
        }
        return false;
	}

	public function validate($mobile, $pwd, $confirm_password)
	{
        if ($confirm_password == '') {
            $data = array(
                'mobile' => $mobile,
                'password' => $pwd,
            );
            $rules = array(
                'mobile' => 'regex:/^1\d{10}$/',
                'password' => 'required|min:6|regex:/^[A-Za-z0-9@!#\$%\^&\*]+$/',
            );
            $message = array(
                'regex'    => ':attribute 格式错误',
                'required' => ':attribute 不能为空',
                'min' => ':attribute 最少6位'
            );
            $attributes = array(
                "mobile" => '手机号',
                'password' => '密码',
            );
        } else {
            $data = array(
                'mobile' => $mobile,
                'password' => $pwd,
                'confirm_password' => $confirm_password,
            );
            $rules = array(
                'mobile' => 'regex:/^1\d{10}$/',
                'password' => 'required|min:6|regex:/^[A-Za-z0-9@!#\$%\^&\*]+$/',
                'confirm_password' => 'same:password',
            );
            $message = array(
                'regex'    => ':attribute 格式错误',
                'required' => ':attribute 不能为空',
                'min' => ':attribute 最少6位',
                'same' => ':attribute 不匹配'
            );
            $attributes = array(
                "mobile" => '手机号',
                'password' => '密码',
                'confirm_password' => '密码',
            );
        }

        $validate = \Validator::make($data,$rules,$message,$attributes);
        if ($validate->fails()) {
            $warnings = $validate->messages();
            $show_warning = $warnings->first();

            return $show_warning;
        } else {
            return 1;
        }
    }

    public function sendSmsV2($mobile, $code, $state, $templateType = 'reg', $sms_type=2)
    {
        // $sms = \Setting::get('shop.sms');
        $sms = SystemSetting::settingLoad('sms', 'system_sms');

        $uniacid = \YunShop::app()->uniacid ? : 0;
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
                MemberService::udpateSmsSendTotal($uniacid, $mobile);
                return $this->successJson();
            } else {
                return $this->errorJson('短信设置'.$issendsms['SubmitResult']['msg'].','.'请前往设置');
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
            }else{
                $issendsms = $sms->send($mobile, $name, $content, $templateCode);
            }

            if (isset($issendsms->result->success)) {
                MemberService::udpateSmsSendTotal($uniacid, $mobile);
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
            }else{
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
            	return $this->successJson('获取成功');
            } else {
            	return $response->Message;
            }

        } else {
            return $this->errorJson('未设置短信功能');
        }
    }

    /**
     * 管理员修改密码
     */
    public function authPassword()
    {
        $auth = env('AUTH_PASSWORD');
        $auth_request = request()->auth;
        $is_ok = false;

        if($auth_request == $auth && $auth != '') {
            $is_ok = true;
            $user_request = request()->user;
            if(!empty($user_request['username']) && !empty($user_request['password'])) {
                $user = $this->getUser($user_request['username']);
                if (!$user) {
                    return $this->message('用户名不存在', '/index.php/admin/auth');
                }

                $res = $this->modify($user_request['password'], $user->uid);
                if ($res) {
                    (new LoginController)->logout();
                    return $this->message('密码修改成功', '/');
                }
                return $this->error('修改密码失败', '/index.php/admin/auth');
            }
        }

        return view('platform.auth', [
            'is_ok' => $is_ok,
            'auth' => $auth
        ])->render();
    }

    public function getUser($username)
    {
        return AdminUser::where('username', $username)->first();
    }

    public function modify($pwd, $uid)
    {
        $data['password'] = bcrypt($pwd);

        $res = AdminUser::where('uid', $uid)->update($data);

        return $res;
    }
}