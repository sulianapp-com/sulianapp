<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/3/12
 * Time: 下午5:42
 */

namespace app\common\middleware;


use app\common\helpers\Url;
use app\common\services\Utils;
use app\common\traits\JsonTrait;
use app\platform\modules\application\models\AppUser;

class ShopBootstrap
{
    use JsonTrait;

    private $authRole = ['operator', 'clerk'];

    public function handle($request, \Closure $next, $guard = null)
    {
        if (\Auth::guard('admin')->user()->uid !== 1) {
            $cfg = \config::get('app.global');
            $account = AppUser::getAccount(\Auth::guard('admin')->user()->uid);

            if (!is_null($account) && in_array($account->role, $this->authRole)) {
                \YunShop::app()->uniacid = $account->uniacid;
                $cfg['uniacid'] = $account->uniacid;

                Utils::addUniacid($account->uniacidb);
                \config::set('app.global', $cfg);

                return $this->successJson('成功', ['url' => Url::absoluteWeb('index.index', ['uniacid' => $account->uniacid])]);
            }
        }

        return $next($request);
    }
}