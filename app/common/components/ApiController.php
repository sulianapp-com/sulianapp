<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/28
 * Time: 上午10:49
 */

namespace app\common\components;

use app\common\exceptions\ShopException;
use app\common\exceptions\UniAccountNotFoundException;
use app\common\helpers\Client;
use app\common\helpers\Url;
use app\common\middleware\BasicInformation;
use app\common\models\Member;
use app\common\models\UniAccount;
use app\common\modules\shop\models\Shop;
use app\frontend\modules\member\services\factory\MemberFactory;

class ApiController extends BaseController
{
    const MOBILE_TYPE = 5;
    const WEB_APP     = 7;
    const NATIVE_APP  = 9;

    protected $publicController = [];
    protected $publicAction = [];
    protected $ignoreAction = [];

    public $jump = false;

    /**
     * @throws ShopException
     * @throws UniAccountNotFoundException
     */
    public function preAction()
    {
        parent::preAction();
        if (!UniAccount::checkIsExistsAccount(\YunShop::app()->uniacid)) {
            throw new UniAccountNotFoundException('无此公众号', ['login_status' => -2]);
        }

        $relaton_set = Shop::current()->memberRelation;

        $mid = Member::getMid();
        $mark = \YunShop::request()->mark;
        $mark_id = \YunShop::request()->mark_id;

        $type = \YunShop::request()->type;

        if (Client::setWechatByMobileLogin(\YunShop::request()->type)) {
            $type = 5;
        }

        if (self::is_alipay()) {
            $type = 8;
        }

        $member = MemberFactory::create($type);

        if (!$member->checkLogged()) {
            if (($relaton_set->status == 1 && !in_array($this->action, $this->ignoreAction))
                || ($relaton_set->status == 0 && !in_array($this->action, $this->publicAction))
            ) {
                $this->jumpUrl($type, $mid);
            }
        } else {
            if (\app\frontend\models\Member::current()->yzMember->is_black) {
                throw new ShopException('黑名单用户，请联系管理员', ['login_status' => -1]);
            }

            //发展下线
            Member::chkAgent(\YunShop::app()->getMemberId(), $mid, $mark ,$mark_id);
        }
    }
    public static function is_alipay()
    {
        if (!empty($_SERVER['HTTP_USER_AGENT']) && strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'alipay') !== false && (app('plugins')->isEnabled('alipay-onekey-login'))) {
            return true;
        }
        return false;
    }

    /**
     * @param $type
     * @param $mid
     * @throws ShopException
     */
    protected function jumpUrl($type, $mid)
    {
        if (empty($type) || $type == 'undefined') {
            $type = Client::getType();
        }

        $scope   = \YunShop::request()->scope ?: '';
        $route   = \YunShop::request()->route;

        $queryString = ['type'=>$type,'i'=>\YunShop::app()->uniacid, 'mid'=>$mid, 'scope' => $scope];

        if ($type == 2 || $type == 11 || $type == 12) {
            return $this->errorJson('请登录', ['login_status' => 0, 'login_url' => Url::absoluteApi('member.login.index', $queryString)]);
        } else {
            if ($scope == 'home' && !$mid) {
                return;
            }

            if ($scope == 'pass') {
                return;
            }

            if (self::MOBILE_TYPE == $type || self::WEB_APP == $type || self::NATIVE_APP == $type) {
                return $this->errorJson('请登录', ['login_status' => 1, 'login_url' => '', 'type' => $type, 'i' => \YunShop::app()->uniacid, 'mid' => $mid, 'scope' => $scope]);
            }

            return $this->errorJson('请登录', ['login_status' => 0, 'login_url' => Url::absoluteApi('member.login.index', $queryString)]);
        }
    }
}