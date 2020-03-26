<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/2/15
 * Time: 下午6:56
 */

namespace app\platform\controllers;


use app\common\services\Utils;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use app\platform\modules\user\models\AdminUser;
use app\platform\modules\system\models\SystemSetting;
use app\platform\modules\application\models\UniacidApp;
use app\platform\modules\application\models\AppUser;
use app\common\helpers\Url;

class LoginController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';
    protected $username;
    private $authRole = ['operator', 'clerk'];


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
   //     $this->middleware('guest:admin', ['except' => 'logout']);
    }

    /**
     * 自定义字段名
     * 可使用
     * @return array
     */
    public function atributeNames()
    {
        return [
            'username' => '用户名',
            'password' => '密码'
        ];
    }

    /**
     * 字段规则
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'required',
            'password' => 'required',
        ];
    }

    /**
     * 重写登录视图页面
     * @return [type]                   [description]
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }
    /**
     * 自定义认证驱动
     * @return [type]                   [description]
     */
    protected function guard()
    {
        return auth()->guard('admin');
    }

    /**
     * 重写验证字段.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    /**
     * Log the user out of the application.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        $this->guard('admin')->logout();
        request()->session()->flush();
        request()->session()->regenerate();

        Utils::removeUniacid();

        return $this->successJson('成功', []);
    }

    /**
     * 重写登录接口
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $request->offsetSet('password',base64_decode($request->password));
        try {
            $this->validate($this->rules(), $request, [], $this->atributeNames());
        } catch (\Exception $e) {
            return $this->errorJson($e->getMessage());
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * 重写登录成功json返回
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        $admin_user = AdminUser::where('uid', $this->guard()->user()->uid);
        $admin_user->update([
            'lastvisit' =>  time(),
            'lastip' => Utils::getClientIp(),
        ]);
        $hasOneAppUser = $admin_user->first()->hasOneAppUser;
        if ($hasOneAppUser->role == 'clerk' || $hasOneAppUser->role == 'operator') {
            $sys_app = UniacidApp::getApplicationByid($hasOneAppUser->uniacid);
            if (!is_null($sys_app->deleted_at)) {
                return $this->successJson('平台已停用', ['status' => -5]);
            } elseif ($sys_app->validity_time !=0 && $sys_app->validity_time < mktime(0,0,0, date('m'), date('d'), date('Y'))) {
                return $this->successJson('平台已过期', ['status' => -5]);
            }
        }

        if ($this->guard()->user()->uid !== 1) {
            $cfg = \config::get('app.global');
            $account = AppUser::getAccount($this->guard()->user()->uid);

            if (!is_null($account) && in_array($account->role, $this->authRole)) {
                $cfg['uniacid'] = $account->uniacid;
                Utils::addUniacid($account->uniacidb);

                \YunShop::app()->uniacid = $account->uniacid;
                \config::set('app.global', $cfg);

                return $this->successJson('成功', ['url' => Url::absoluteWeb('index.index', ['uniacid' => $account->uniacid])]);
            }
        }

        return $this->successJson('成功');
    }

    /**
     * 重写登录失败json返回
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendFailedLoginResponse(Request $request)
    {
        return $this->errorJson(Lang::get('auth.failed'), []);
    }

    /**
     * 重写登录失败次数限制
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        $message = Lang::get('auth.throttle', ['seconds' => $seconds]);

        return $this->errorJson($message);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request), 1
        );
    }

    public function site()
    {
        $default = [
            'name' => "芸众商城管理系统",
            'site_logo' => yz_tomedia("/static/images/site_logo.png"),
            'title_icon' => yz_tomedia("/static/images/title_icon.png"),
            'advertisement' => yz_tomedia("/static/images/advertisement.jpg"),
            'information' => '<p>&copy; 2019&nbsp;<a href="https://www.yunzmall.com/" target=\"_blank\" rel=\"noopener\">Yunzhong.</a>&nbsp;All Rights Reserved. 广州市芸众信息科技有限公司&nbsp;&nbsp;<a href="http://www.miit.gov.cn/" target="_blank\" rel="noopener\">&nbsp;粤ICP备17018310号-1</a>&nbsp;Powered by Yunzhong&nbsp;</p> <p><a href="https://bbs.yunzmall.com" target="_blank\" rel="noopener\">系统使用教程：bbs.yunzmall.com</a>&nbsp; &nbsp;&nbsp;<a href="https://bbs.yunzmall.com/plugin.php?id=it618_video:index" target="_blank\" rel="noopener\">视频教程</a></p>'
        ];

        $copyright = SystemSetting::settingLoad('copyright', 'system_copyright');

        if (!empty($copyright)) {
            isset($copyright['name']) ? $copyright['name'] : '';
            isset($copyright['site_logo']) ? $copyright['site_logo'] : '';
            isset($copyright['title_icon']) ? $copyright['title_icon'] : '';
            isset($copyright['advertisement']) ? $copyright['advertisement'] : $copyright['advertisement'] = $default['advertisement'];
            isset($copyright['information']) ? $copyright['information'] : '';
        } else {
            $copyright['name'] = $default['name'];
            $copyright['site_logo'] = $default['site_logo'];
            $copyright['title_icon'] = $default['title_icon'];
            $copyright['advertisement'] = $default['advertisement'];
            $copyright['information'] = $default['information'];
        }

        if ($copyright) {
            return $this->successJson('成功', $copyright);
        } else {
            return $this->errorJson('没有检测到数据', '');
        }
    }
}