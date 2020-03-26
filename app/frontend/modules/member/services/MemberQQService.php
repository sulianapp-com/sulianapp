<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/23
 * Time: 上午11:20
 */

namespace app\frontend\modules\member\services;

use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\member\models\MemberQQModel;
use Illuminate\Session\Store;

class MemberQQService extends MemberService
{
    private $_login_type    = 6;

    public function __construct()
    {}

    public function login()
    {
        $uniacid      = \YunShop::app()->uniacid;

        $appId        = \YunShop::app()->account['key'];
        $appSecret    = \YunShop::app()->account['secret'];
        $code         = \YunShop::request()->code;
        $url          = \YunShop::app()->siteroot . 'app/index.php?' . $_SERVER['QUERY_STRING'];

        $authurl = $this->_getAuthUrl($appId, $url);
        $tokenurl = $this->_getTokenUrl($appId, $appSecret, $code);

        if (!empty($code)) {
            $resp     = \Curl::to($tokenurl)->get();
            $token    = @json_decode($resp['content'], true);

            if (!empty($token) && is_array($token) && $token['errmsg'] == 'invalid code') {
                show_json(0, array('msg'=>'请求错误'));
            }

            $openid_url = $this->_getOpenIdUrl($token['accesstoken'], $token['openid']);
            @\Curl::to($openid_url)->get();

            $userinfo_url = $this->_getUserInfoUrl($token['accesstoken'], $token['openid']);
            $userinfo = @\Curl::to($userinfo_url)->get();

            if (is_array($userinfo) && !empty($userinfo['unionid'])) {
                $UnionidInfo = MemberUniqueModel::getUnionidInfo($uniacid, $userinfo['unionid']);

                $types = explode($UnionidInfo['type'], '|');

                if ($UnionidInfo['unionid']) {
                    if (!in_array($this->_login_type, $types)) {
                        //更新ims_yz_member_unique表
                        MemberUniqueModel::updateData(array(
                            'unique_id'=>$UnionidInfo['unique_id'],
                            'type' => $UnionidInfo['type'] . '|' . $this->_login_type
                        ));
                    }

                    $_SESSION['member_id'] = $UnionidInfo['member_id'];
                } else {
                    $member_id = McMappingFansModel::getUId($uniacid, $token['openid']);
                    //添加ims_yz_member_unique表
                    MemberUniqueModel::insertData(array(
                        'uniacid' => $uniacid,
                        'unionid' => $token['unionid'],
                        'member_id' => $member_id,
                        'type' => $this->_login_type
                    ));

                    session()->put('member_id',$member_id);
                }
            } else {
                show_json(0, array('url'=> $authurl));
            }
        } else {
            show_json(0, array('url'=> $authurl));
        }

        show_json(1, array('member_id', $_SESSION['member_id']));
    }

    private function _getAuthUrl($app_id, $url)
    {
        return " https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id={$app_id}&redirect_uri={$url}&state=1234";
    }

    private function _getTokenUrl($app_id, $app_secret, $code, $url)
    {
        return "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id=" . $app_id . "&client_secret=" . $app_secret. "&code=" . $code . "&redirect_uri=" . $url;
    }

    private function _getOpenIdUrl($access_token)
    {
        return "https://graph.qq.com/oauth2.0/me?access_token" . $access_token;
    }

    private function _getUserInfoUrl($accesstoken, $openid)
    {
        return "https://graph.qq.com/user/get_simple_userinfo? access_token={$accesstoken}&oauth_consumer_key=12345&openid={$openid} ";
    }

    /**
     * 验证登录状态
     *
     * @return bool
     */
    public function checkLogged($login = null)
    {
        return MemberService::isLogged();
    }
}