<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/10/17
 * Time: 下午3:19
 */

namespace app\common\services\api;


class WechatApi
{

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
    public function _getAuthUrl($appId, $url, $state)
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
    public function _getAuthBaseUrl($appId, $url, $state)
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
    public function _getTokenUrl($appId, $appSecret, $code)
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
    public function _getUserInfoUrl($accesstoken, $openid)
    {
        return "https://api.weixin.qq.com/sns/userinfo?access_token={$accesstoken}&openid={$openid}&lang=zh_CN";
    }

    /**
     * 获取全局ACCESS TOKEN
     * @return string
     */
    public function _getAccessToken($appId, $appSecret)
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
    public function _getInfo($accesstoken, $openid)
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
    public function _tokenAuth($accesstoken, $openid)
    {
        return 'https://api.weixin.qq.com/sns/auth?access_token=' . $accesstoken . '&openid=' . $openid;
    }

    public function _refreshAuth($appid, $refreshtoken)
    {
        return 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=' . $appid . '&grant_type=refresh_token&refresh_token=' . $refreshtoken;
    }

    public function baseUserInfo($appId, $appSecret, $openid)
    {
        $global_access_token_url = $this->_getAccessToken($appId, $appSecret);

        $global_token = \Curl::to($global_access_token_url)
            ->asJsonResponse(true)
            ->get();

        $global_userinfo_url = $this->_getInfo($global_token['access_token'], $openid);

        $user_info = \Curl::to($global_userinfo_url)
            ->asJsonResponse(true)
            ->get();


        return $user_info;
    }

    public function authUserInfo($accessToken, $openid)
    {
        $userinfo_url = $this->_getUserInfoUrl($accessToken, $openid);

        $user_info = \Curl::to($userinfo_url)
            ->asJsonResponse(true)
            ->get();

        return $user_info;
    }
}