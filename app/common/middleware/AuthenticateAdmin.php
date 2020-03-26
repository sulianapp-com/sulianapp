<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/2/19
 * Time: 下午5:08
 */

namespace app\common\middleware;

use app\common\services\Utils;
use app\common\traits\JsonTrait;
use app\platform\modules\application\models\AppUser;
use Closure;

class AuthenticateAdmin
{
    use JsonTrait;

    /**
     * 公众号状态
     *
     */
    const UNIACID_STATUS = -1;

    /**
     * 用户状态
     *
     */
    const USER_STATUS = -2;

    /**
     * API访问状态
     *
     */
    const API_STATUS = -3;

    /**
     * 公共接口
     *
     * @var array
     */
    protected $except = [
        'admin/index',
    ];

    /**
     * 非管理员有效访问接口
     *
     * @var array
     */
    protected $authApi = [
        'admin/index',
        'admin/shop',
        'admin/application',
        'admin/application/recycle',
        'admin/appuser',
        'admin/appuser/add',
        'admin/appuser/delete',
        'admin/appuser/checkname',
        'admin/all/upload',
        'admin/application/getApp',
        'admin/application/delete/{id}',
        'admin/application/add',
        'admin/application/checkAddRole',
        'admin/application/update/{id}',
        'admin/application/switchStatus/{id}',
        'admin/application/setTop/{id}',
        'admin/all/list',
        'admin/all/delImg',
        'admin/user/modify_user',
        'admin/user/send_code',
        'admin/user/send_new_code',
        'admin/user/modify_mobile'
    ];

    /**
     * 访问用户
     *
     * @var null
     */
    private $account = null;

    /**
     * 公众号
     *
     * @var int
     */
    private $uniacid = 0;

    /**
     * 用户角色
     *
     * @var array
     */
    private $role = ['role' => '', 'isfounder' => false];

    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $cfg   = \config::get('app.global');
        $check = $this->checkUserInfo();
        $uri   = \Route::getCurrentRoute()->getUri();

        if (!$check['result']) {
            return $this->errorJson($check['msg'], ['status' => self::USER_STATUS]);
        }

        if (\Auth::guard('admin')->user()->uid == 1) {
            \YunShop::app()->role = 'founder';
            \YunShop::app()->isfounder = true;

            $this->role = ['role' => 'founder', 'isfounder' => true];
        } else {
            if (!in_array($uri, $this->authApi)) {

                return $this->errorJson('无访问权限', ['status' => self::API_STATUS]);
            }

            if (!empty($cfg['uniacid'])) {
                $this->uniacid = $cfg['uniacid'];
                $this->account = AppUser::getAccount(\Auth::guard('admin')->user()->uid, $cfg['uniacid']);

                if (!is_null($this->account)) {
                    $this->setRole();
                } else {
                    $this->relogin();
                }
            }
        }

        \config::set('app.global', array_merge($this->setConfigInfo(), $this->role));

        return $next($request);
    }

    /**
     * 获取全局参数
     *
     * @return array
     */
    private function setConfigInfo()
    {
        return \config::get('app.global');
    }

    /**
     * 获取用户身份
     *
     * @return array
     */
    private function setRole()
    {
        if (\Auth::guard('admin')->user()->uid === 1) {
            \YunShop::app()->role = 'founder';
            \YunShop::app()->isfounder = true;

            $this->role = ['role' => 'founder', 'isfounder' => true];
        } else {
            \YunShop::app()->role = $this->account->role;
            \YunShop::app()->isfounder = false;

            $this->role = ['role' => $this->account->role, 'isfounder' => false];
        }
    }

    /**
     * 验证访问权限
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function relogin()
    {
        \Auth::guard('admin')->logout();
        request()->session()->flush();
        request()->session()->regenerate();

        Utils::removeUniacid();

        return $this->errorJson('请重新登录', ['login_status' => 1, 'login_url' => '/#/login']);

    }

    /**
     * 检测用户信息
     *
     * @return array
     */
    private function checkUserInfo()
    {
        $user   = \Auth::guard('admin')->user();
        $result = 1;

        if ($user->status == 3) {
            $result = 0;
            $msg    = '您已被禁用，请联系管理员';
        }
        if ($user->endtime != 0 && $user->endtime <= time()) {
            $result = 0;
            $msg    = '您的账号已过期，请联系管理员';
        }

        return [
            'result' => $result,
            'msg'    => $msg
        ];
    }

    /**
     * 获取错误信息
     *
     * @return mixed
     */
    private function errorMsg()
    {
        if (\Cache::has('app.access')) {
            $msg = \Cache::get('app.access');

            \Cache::forget('app.access');
            Utils::removeUniacid();

            return $msg;
        }
    }
}