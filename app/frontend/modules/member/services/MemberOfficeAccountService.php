<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/22
 * Time: 下午4:44
 */

namespace app\frontend\modules\member\services;

use app\common\facades\Setting;
use app\common\helpers\Cache;
use app\common\helpers\Client;
use app\common\helpers\Url;
use app\common\models\AccountWechats;
use app\common\models\Member;
use app\common\services\Session;
use app\frontend\modules\member\models\McMappingFansModel;
use app\frontend\modules\member\models\MemberUniqueModel;
use app\frontend\modules\member\models\SubMemberModel;
use Illuminate\Contracts\Encryption\DecryptException;

class MemberOfficeAccountService extends MemberService
{
    const LOGIN_TYPE = '1';

    public function __construct()
    {
    }

    public function login()
    {
        $member_id = 0;

        $uniacid = \YunShop::app()->uniacid;
        $scope   = \YunShop::request()->scope ?: 'userinfo';     //scope: base|home|userinfo

        if (Setting::get('shop.member')['wechat_login_mode'] == '1') {
            return $this->isPhoneLogin($uniacid);
        }

        $code = \YunShop::request()->code;

        $account = AccountWechats::getAccountByUniacid($uniacid);
        $appId = $account->key;
        $appSecret = $account->secret;

        $callback = ($_SERVER['REQUEST_SCHEME'] ? $_SERVER['REQUEST_SCHEME'] : 'http')  . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        $state = 'yz-' . session_id();

        $authurl = $this->_getAuthUrl($appId, $callback, $state);

        if ($scope == 'base') {

            $authurl = $this->_getAuthBaseUrl($appId, $callback, $state);
        }

        if (!empty($code)) {
            $redirect_url = $this->_getClientRequestUrl();

            $tokenurl = $this->_getTokenUrl($appId, $appSecret, $code);

            $token = \Curl::to($tokenurl)
                ->asJsonResponse(true)
                ->get();

            if (!empty($token) && !empty($token['errmsg']) && $token['errmsg'] == 'invalid code') {
                return show_json(5, 'token请求错误');
            }

            $userinfo = $this->getUserInfo($appId, $appSecret, $token);

            if (is_array($userinfo) && !empty($userinfo['errcode'])) {
                \Log::debug('微信登陆授权失败-', $userinfo);
                return show_json(-3, '微信登陆授权失败');
            }

            //Login
            $member_id = $this->memberLogin($userinfo);

            Session::set('member_id', $member_id);

            setcookie('Yz-Token', encrypt($userinfo['access_token'] . '\t' . ($userinfo['expires_in'] + time()) . '\t' . $userinfo['openid'] . '\t' . $scope), time() + self::TOKEN_EXPIRE);
        } else {
            $this->_setClientRequestUrl();

            redirect($authurl)->send();
            exit;
        }

        redirect($redirect_url)->send();
        exit;
    }

    /**
     * 获取用户信息
     *
     * @param $appId
     * @param $appSecret
     * @param $token
     * @return mixed
     */
    public function getUserInfo($appId, $appSecret, $token)
    {
        $scope     = \YunShop::request()->scope ?: '';
        $subscribe = 0;
        $share = Setting::get('shop.share');
        $user_info = [];

        if (is_null($share) || $share['follow'] == 1 || ($share && is_null($share['follow']))) {
            $global_access_token_url = $this->_getAccessToken($appId, $appSecret);

            $global_token = \Curl::to($global_access_token_url)
                ->asJsonResponse(true)
                ->get();

            $global_userinfo_url = $this->_getInfo($global_token['access_token'], $token['openid']);

            $user_info = \Curl::to($global_userinfo_url)
                ->asJsonResponse(true)
                ->get();

            $subscribe = $user_info['subscribe'];
        }

        if (0 == $subscribe && $scope != 'base') { //未关注拉取不到用户信息
            $userinfo_url = $this->_getUserInfoUrl($token['access_token'], $token['openid']);

            $user_info = \Curl::to($userinfo_url)
                ->asJsonResponse(true)
                ->get();

            $user_info['subscribe'] = $subscribe;
        }

        return array_merge($user_info, $token);
    }

    /**
     * 用户验证授权 api
     *
     * snsapi_userinfo
     *
     * @param $appId
     * @param $url
     * @param $state
     * @return string
     */
    private function _getAuthUrl($appId, $url, $state)
    {
        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appId . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_userinfo&state={$state}#wechat_redirect";
    }

    /**
     *
     * 静默获取用户信息
     *
     * snsapi_base
     *
     * @param $appId
     * @param $url
     * @param $state
     * @return string
     */
    private function _getAuthBaseUrl($appId, $url, $state)
    {
        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appId . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_base&state={$state}#wechat_redirect";
    }

    /**
     * 获取token api
     *
     * @param $appId
     * @param $appSecret
     * @param $code
     * @return string
     */
    private function _getTokenUrl($appId, $appSecret, $code)
    {
        return "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appId . "&secret=" . $appSecret . "&code=" . $code . "&grant_type=authorization_code";
    }

    /**
     * 获取用户信息 api
     *
     * 无需关注
     *
     * @param $accesstoken
     * @param $openid
     * @return string
     */
    private function _getUserInfoUrl($accesstoken, $openid)
    {
        return "https://api.weixin.qq.com/sns/userinfo?access_token={$accesstoken}&openid={$openid}&lang=zh_CN";
    }

    /**
     * 获取全局ACCESS TOKEN
     * @return string
     */
    private function _getAccessToken($appId, $appSecret)
    {
        return 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appId . '&secret=' . $appSecret;
    }

    /**
     * 获取用户信息
     *
     * 需要关注
     *
     * @param $accesstoken
     * @param $openid
     * @return string
     */
    private function _getInfo($accesstoken, $openid)
    {
        return 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $accesstoken . '&openid=' . $openid;
    }

    /**
     * 验证account_token
     *
     * @param $accesstoken
     * @param $openid
     *
     * @return string
     */
    private function _tokenAuth($accesstoken, $openid)
    {
        return 'https://api.weixin.qq.com/sns/auth?access_token=' . $accesstoken . '&openid=' . $openid;
    }

    private function _refreshAuth($appid, $refreshtoken)
    {
        return 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=' . $appid . '&grant_type=refresh_token&refresh_token=' . $refreshtoken;
    }

    /**
     * 设置客户端请求地址
     *
     * @return string
     */
    private function _setClientRequestUrl()
    {
        $pattern = '/(&t=([\d]+[^&]*))/';
        $t = time();

        if (\YunShop::request()->yz_redirect) {
            $yz_redirect = base64_decode(\YunShop::request()->yz_redirect);

            if (preg_match($pattern, $yz_redirect)) {
                $redirect_url = preg_replace($pattern, "&t={$t}", $yz_redirect);
            } else {
                $redirect_url = $yz_redirect . '&t=' . time();
            }

            Session::set('client_url', $redirect_url);
        } else {
            Session::set('client_url', '');
        }
    }

    private function _setClientRequestUrl_v2()
    {
        $redirect_url = '';
        $pattern = '/(&t=([\d]+[^&]*))/';
        $t = time();

        if (\YunShop::request()->yz_redirect) {
            $yz_redirect = base64_decode(\YunShop::request()->yz_redirect);

            if (preg_match($pattern, $yz_redirect)) {
                $redirect_url = preg_replace($pattern, "&t={$t}", $yz_redirect);
            } else {
                $redirect_url = $yz_redirect . '&t=' . time();
            }
        }

        return urlencode($redirect_url);
    }

    /**
     * 获取客户端地址
     *
     * @return mixed
     */
    private function _getClientRequestUrl()
    {
        $url = Session::get('client_url') ?: $this->_getFrontJumpUrl();

        if ($url === false || $url == '') {
            $url = Url::absoluteApp('home') . '&t=' . time();
        }

        return $url;
    }

    private function _getFrontJumpUrl()
    {
        $redirect_url = '';
        $pattern = '/(&t=([\d]+[^&]*))/';
        $t = time();

        if (\YunShop::request()->yz_redirect) {
            $yz_redirect = base64_decode(\YunShop::request()->yz_redirect);

            if (preg_match($pattern, $yz_redirect)) {
                $redirect_url = preg_replace($pattern, "&t={$t}", $yz_redirect);
            } else {
                $redirect_url = $yz_redirect . '&t=' . time();
            }
        }

        \Log::debug('-----------------front_url----------------', [$redirect_url]);

        return $redirect_url;
    }

    /**
     * 公众号开放平台授权登陆
     *
     * @param $uniacid
     * @param $userinfo
     * @return array|int|mixed
     */
    public function unionidLogin($uniacid, $userinfo, $upperMemberId = null)
    {
        $member_id = parent::unionidLogin($uniacid, $userinfo, $upperMemberId, self::LOGIN_TYPE);

        return $member_id;
    }

    public function updateMemberInfo($member_id, $userinfo)
    {
        parent::updateMemberInfo($member_id, $userinfo);
        \Log::debug('----update_mapping_fans----', $member_id);
        $record = array(
            //'openid' => $userinfo['openid'],
            'nickname' => stripslashes($userinfo['nickname']),
            'follow' => $userinfo['subscribe'] ?: 0,
            'tag' => base64_encode(serialize($userinfo))
        );

        McMappingFansModel::updateData($member_id, $record);
    }

    public function addMemberInfo($uniacid, $userinfo)
    {
        $uid = parent::addMemberInfo($uniacid, $userinfo);

        \Log::debug('----mapping_fans----', $uid);
        //添加mapping_fans表
        $this->addFansMember($uid, $uniacid, $userinfo);

        return $uid;
    }

    public function addFansMember($uid, $uniacid, $userinfo)
    {
        McMappingFansModel::insertData($userinfo, array(
            'uid' => $uid,
            'acid' => $uniacid,
            'uniacid' => $uniacid,
            'salt' => Client::random(8),
        ));
    }

    public function getFansModel($openid)
    {
        return McMappingFansModel::getFansData($openid);
    }

    /**
     * 会员关联表操作
     *
     * @param $uniacid
     * @param $member_id
     * @param $unionid
     */
    public function addMemberUnionid($uniacid, $member_id, $unionid)
    {
        MemberUniqueModel::insertData(array(
            'uniacid' => $uniacid,
            'unionid' => $unionid,
            'member_id' => $member_id,
            'type' => self::LOGIN_TYPE
        ));
    }

    public function updateFansMember($fanid, $member_id, $userinfo)
    {
        $record = array(
            'uid'       => $member_id,
            'nickname' => stripslashes($userinfo['nickname']),
            'follow' => isset($userinfo['subscribe']) ? $userinfo['subscribe'] : 0,
            'tag' => base64_encode(serialize($userinfo))
        );

        McMappingFansModel::updateDataById($fanid, $record);
    }

    protected function updateSubMemberInfoV2($uid, $userinfo)
    {
        SubMemberModel::updateOpenid(
            $uid, [
                'yz_openid' => $userinfo['openid'],
                'access_token_1' => $userinfo['access_token'],
                'access_expires_in_1' => time() + $userinfo['expires_in'],
                'refresh_token_1' => $userinfo['refresh_token'],
                'refresh_expires_in_1' => time() + (28 * 24 * 3600)
            ]
        );
    }

    /**
     * 添加会员主表信息
     *
     * @param $uniacid
     * @param $userinfo
     * @return mixed
     */
    public function addMcMemberInfo($uniacid, $userinfo)
    {
        $uid = parent::addMemberInfo($uniacid, $userinfo);

        return $uid;
    }

    /**
     * 判断是否为手机登录
     * @param $uniacid
     * @return array
     */
    public function isPhoneLogin($uniacid)
    {
        $mid = Member::getMid();
        $type = \YunShop::request()->type ;
        $mobile   = \YunShop::request()->mobile;
        $password = \YunShop::request()->password;

        $yz_redirect = \YunShop::request()->yz_redirect;
        if ($mobile && $password) {
            $res =  (new MemberMobileService)->login();
            if ($res['status'] == 1) {
                $redirect_url = $this->_getClientRequestUrl();
                $res['json']['redirect_url'] = $redirect_url;
            }
            return $res;
        } else {
            $this->_setClientRequestUrl();
            $redirect_url = Url::absoluteApp('login', ['i' => $uniacid, 'type' => $type, 'mid' => $mid, 'yz_redirect' => $yz_redirect]);
            redirect($redirect_url)->send();
        }
    }

    public function chekAccount()
    {
        $uniacid = \YunShop::app()->uniacid;
        $code = \YunShop::request()->code;
        $account = AccountWechats::getAccountByUniacid($uniacid);
        $appId = $account->key;
        $appSecret = $account->secret;
        $state = 'yz-' . session_id();

        $callback = ($_SERVER['REQUEST_SCHEME'] ? $_SERVER['REQUEST_SCHEME'] : 'http')  . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        $authurl = $this->_getAuthBaseUrl($appId, $callback, $state);
        $tokenurl = $this->_getTokenUrl($appId, $appSecret, $code);

        if (!empty($code)) {
            $redirect_url = $this->_getClientRequestUrl();

            $token = \Curl::to($tokenurl)
                ->asJsonResponse(true)
                ->get();

            if (!empty($token) && !empty($token['errmsg']) && $token['errmsg'] == 'invalid code') {
                return show_json(5, 'token请求错误');
            }

            $userinfo = $this->getUserInfo($appId, $appSecret, $token);

            if (is_array($userinfo) && !empty($userinfo['errcode'])) {
                \Log::debug('微信登陆授权失败-'. $userinfo['errcode']);
                return show_json(-3, '微信登陆授权失败');
            }

            $fans_info = McMappingFansModel::getFansById(\YunShop::app()->getMemberId());

            if ($fans_info->openid != $userinfo['openid']) {
                \Log::debug('----openid error----', [$fans_info->uid, $userinfo['openid']]);
                session_destroy();
                Cache::forget($fans_info->uid . ':chekAccount');
                redirect($redirect_url)->send();
            }
        } else {
            $this->_setClientRequestUrl();

            redirect($authurl)->send();
            exit;
        }

        redirect($redirect_url)->send();
        exit;
    }

    public function checkLogged($login = null)
    {
        $uniacid = \YunShop::app()->uniacid;
        $from    = \YunShop::request()->scope;

        if (isset($_COOKIE['Yz-Token'])) {
            try {
                $yz_token = decrypt($_COOKIE['Yz-Token']);

                list($token, $expires, $openid, $scope) = explode('\t', $yz_token);
            } catch (DecryptException $e) {
                return false;
            }

            if ($scope === 'base' && $from != $scope) {
                $login->jump = true;
                return false;
            }

            $yz_member = SubMemberModel::getMemberByWechatTokenAndOpenid($token, $openid);
            
            if (is_null($yz_member)) {
                $openid_member = SubMemberModel::getMemberByOpenid($openid);

                if (!is_null($openid_member) && $openid_member->access_expires_in_1 > $expires) {
                    if (\YunShop::app()->getMemberId() != $openid_member->member_id) {
                        Session::set('member_id', $openid_member->member_id);
                    }

                    return true;
                }

                return false;
            }

            if ($yz_member->access_expires_in_1 > time()) {
                if (\YunShop::app()->getMemberId() != $yz_member->member_id) {
                    Session::set('member_id', $yz_member->member_id);
                }

                return true;
            } else {
                if ($yz_member->refresh_expires_in_1 > time()) {
                    $account = AccountWechats::getAccountByUniacid($uniacid);
                    $appId = $account->key;
                    $appSecret = $account->secret;

                    $this->updateWechatUserData($yz_member, $appId, $appSecret, $openid);

                    //更新token
                    $refresh_url = $this->_refreshAuth($appId, $yz_member->refresh_token_1);

                    $refresh_info = \Curl::to($refresh_url)
                        ->asJsonResponse(true)
                        ->get();

                    if (!isset($refresh_info['errcode'])) {
                        $yz_member->yz_openid = $refresh_info['openid'];
                        $yz_member->access_token_1 = $refresh_info['access_token'];
                        $yz_member->access_expires_in_1 = time() + 7200;
                        $yz_member->refresh_token_1 = $refresh_info['refresh_token'];
                        $yz_member->refresh_expires_in_1 = time() + (28 * 24 * 3600);
                        $yz_member->save();

                        Session::set('member_id', $yz_member->member_id);
                        setcookie('Yz-Token', encrypt($refresh_info['access_token'] . '\t' . $yz_member->access_expires_in_1 . '\t' . $refresh_info['openid']), time() + self::TOKEN_EXPIRE);
                        return true;
                    }
                } else {
                    return false;
                }
            }
        }

         return false;
    }

    /**
     * 同步微信用户信息
     *
     * @param $yz_member
     * @param $appId
     * @param $appSecret
     * @param $openid
     */
    private function updateWechatUserData($yz_member, $appId, $appSecret, $openid)
    {
        $global_access_token_url = $this->_getAccessToken($appId, $appSecret);

        $global_token = \Curl::to($global_access_token_url)
            ->asJsonResponse(true)
            ->get();

        $global_userinfo_url = $this->_getInfo($global_token['access_token'], $openid);

        $user_info = \Curl::to($global_userinfo_url)
            ->asJsonResponse(true)
            ->get();

        $user_info['nickname'] = $this->filteNickname($user_info);

        if ($user_info['subscribe'] != $yz_member->hasOneMappingFans->follow
            || $user_info['nickname'] != $yz_member->hasOneMember->nickname
            || $user_info['headimgurl'] != $yz_member->hasOneMember->avatar) {

            if ($user_info['subscribe']) {
                $this->updateMemberInfo($yz_member->member_id, $user_info);
            } else {
                  $yz_member->hasOneMappingFans->follow = $user_info['subscribe'];

                  $yz_member->push();
            }
        }
    }
}
