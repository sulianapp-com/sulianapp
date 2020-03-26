<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/2/19
 * Time: 上午11:53
 */

namespace app\common\middleware;


use app\common\traits\JsonTrait;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    use JsonTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, \Closure $next, $guard = null)
    {
        $this->install();

        if (Auth::guard($guard)->guest()) {
            $login_path = [
                'admin' => '/#/login',
            ];
            $url = empty($guard) ? '/login' : (isset($login_path[$guard]) ? $login_path[$guard] : '/login');

            if (strpos($_SERVER['REQUEST_URI'], '/admin/shop') !== false) {
                return redirect()->guest('/');
            }
            return $this->errorJson('请登录', ['login_status' => 1, 'login_url' => $url]);
        }

        return $next($request);
    }

    private function install()
    {
        $path = 'addons/yun_shop';
        $file = $path .  '/api.php';

        if (!file_exists($file)) {
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            $f_data = file_get_contents('api.php');

            file_put_contents($file, $f_data);
        }

        if (!file_exists(base_path().'/bootstrap/install.lock')){
            return $this->errorJson('您还没有操作安装向导，请重试', ['status' => -4]);
        }
    }
}