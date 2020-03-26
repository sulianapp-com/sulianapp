<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/4/1
 * Time: 下午1:13
 */

namespace app\common\middleware;


use app\platform\modules\application\models\UniacidApp;
use Closure;

class AuthenticateShop
{
    public function handle($request, Closure $next)
    {
        $cfg = \config::get('app.global');

        if (!$this->validateUniacid($cfg)) {
            return $this->redirectToHome();
        }

        return $next($request);
    }

    /**
     * 验证uniacid是否有效
     *
     */
    private function validateUniacid($cfg)
    {
        $msg     = '';
        $sys_app = UniacidApp::getApplicationByid($cfg['uniacid']);

        if (is_null($sys_app)) {
            $msg = '非法请求';
        }

        if (!is_null($sys_app->deleted_at)) {
            $msg = '平台已停用';
        }

        if ($sys_app->validity_time !=0 && $sys_app->validity_time < mktime(0,0,0, date('m'), date('d'), date('Y'))) {
            $msg = '平台已过期';
        }

        if ($msg) {
            // \Cache::put('app.access', $msg, 1);

            return false;
        }

        return true;
    }

    /**
     * 链接跳转
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function redirectToHome()
    {
        return redirect()->guest();
    }
}