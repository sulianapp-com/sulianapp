<?php

namespace app\frontend\modules\member\services;

use app\common\services\Session;
use app\frontend\modules\member\services\MemberService;
use app\common\services\alipay\request\AlipaySystemOauthTokenRequest;
use app\common\services\alipay\request\AlipayUserInfoShareRequest;
use app\common\services\alipay\AopClient;
use app\common\helpers\Url;
use app\frontend\modules\member\models\MemberModel;
use Yunshop\AlipayOnekeyLogin\models\MemberAlipay;

class MemberAlipayService extends MemberService
{
	const LOGIN_TYPE = 8;
	

    private $url = "https://openauth.alipay.com/oauth2/publicAppAuthorize.htm";  
    //支付宝网关
    private $alipay_api = "https://openapi.alipay.com/gateway.do"; 

    private $aop; 

    public function __construct()
    {
    	// parent::__construct();

    	$this->aop = $this->aopClient();
    }

	public function login()
	{
        //商城默认头像
        $member_avatar = \Setting::get('shop.member')['headimg'];
		$uniacid = \YunShop::app()->uniacid;
        // $appId = \YunShop::app()->app_id;
        $code = \YunShop::request()->auth_code;

        //回调域名
		$host = ($_SERVER['REQUEST_SCHEME'] ? $_SERVER['REQUEST_SCHEME'] : 'http')  . '://' . $_SERVER['HTTP_HOST'];
        $callback = urlencode($host.$_SERVER['PHP_SELF']);
        //回调地址
        if($_SERVER['QUERY_STRING']) $callback = $callback.'?'.$_SERVER['QUERY_STRING'];

		if (empty($code)) {

            $this->_setClientRequestUrl();

			$alipay_redirect = $this->__CreateOauthUrlForCode($this->aop->appId, $callback);
			redirect($alipay_redirect)->send();
			exit();
		}

		$request = new AlipaySystemOauthTokenRequest();
		$request->setGrantType("authorization_code");
		$request->setCode($code);//这里传入 code
		$result = $this->aop->execute($request);
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$result = json_decode($result, true);
		$user = $result[$responseNode];

		if ($user = $result[$responseNode]) {

			//第一步获取到支付宝用户user_id,判断商城是否已存在这个用户
			//第二步用户已存在则直接登录，不存在则 通过 access_token 获取用户信息添加到支付宝用户表

			$userInfo = $this->getUserInfo($user['access_token']);

			$alipay_user['openid'] = $userInfo['user_id'];
			$alipay_user['nickname'] = $userInfo['nick_name'];
            $alipay_user['headimgurl'] = !empty($userInfo['avatar'])?$userInfo['avatar']:$member_avatar;
			$alipay_user['sex'] = $userInfo['gender'] == 'F' ? 0 : 1;
			$alipay_user['province'] =  $userInfo['province'];
			$alipay_user['city'] =  $userInfo['city'];
			$alipay_user['country'] =  '';

			$member_id = $this->memberLogin($alipay_user);

            Session::set('member_id', $member_id);

            //添加 yz_member_alipay 表
            $bool = MemberAlipay::insertData($userInfo, ['member_id' =>$member_id, 'uniacid' => $uniacid]);

            if (!$bool) {
            	\Log::debug('支付宝用户信息保存失败 user_id：'.$userInfo['user_id'].'会员uid：'.$member_id);
            }

			/*alipay_system_oauth_token_response" => array:6 [
    			"access_token" => "authusrB6e094cbc0cd54ed18e69b35e2000aX41"
    			"alipay_user_id" => "20880051464321899646564260914141"
    			"expires_in" => 1296000
    			"re_expires_in" => 2592000
    			"refresh_token" => "authusrB070ef1be4ccb4c98abd97ca781faaX41"
    			"user_id" => "2088212325598416
    		]*/

		} else {
			/*error_response" => array:4 [
    			"code" => "40002"
    			"msg" => "Invalid Arguments"
    			"sub_code" => "isv.code-invalid"
    			"sub_msg" => "授权码code无效"
  			]*/
  			\Log::debug('支付宝授权失败code:'.$result['error_response']['code']);
			return show_json(-3, '支付宝授权失败');
		}

		$redirect_url = $this->_getClientRequestUrl();
		redirect($redirect_url)->send();
        exit;
		//return show_json(1, Session::get('member_id'));
	}

	private function aopClient()
	{
		$alipay_set = \Setting::get('plugin.alipay_onekey_login');
		$aop = new AopClient;
		$aop->gatewayUrl = $this->alipay_api;
		$aop->appId = $alipay_set['app']['alipay_appid'];
		$aop->rsaPrivateKey = base64_decode($alipay_set['app']['private_key']);
		$aop->alipayrsaPublicKey = base64_decode($alipay_set['app']['alipay_public_key']);
		$aop->signType=  $alipay_set['rsa'] == 1 ? 'RSA' : 'RSA2';
		$aop->apiVersion = '1.0';
		$aop->format = "json";
		$aop->postCharset = "UTF-8";

		return $aop;
	}

	/**
	* 根据access_token 获取用户信息
	* @param ))
	* @return 返回用户信息
	*/
	protected function getUserInfo($access_token)
	{
		$request = new AlipayUserInfoShareRequest();
		$info = $this->aop->execute ($request,$access_token); //这里传入获取的access_token
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$json_info = json_decode($info, true);

		if (!empty($json_info['error_response'])) {
			\Log::debug('支付宝获取用户信息失败code:'.$json_info['error_response']['code']);
            return show_json(-3, '支付宝授权失败');
		}

		return $json_info[$responseNode];
	}

	public function getFansModel($user_id)
    {
        $model = MemberAlipay::getUid($user_id);

        if (!is_null($model)) {
            $model->uid = $model->member_id;
        }

        return $model;
    }

     /**
     * 设置客户端请求地址
     *
     * @return string
     */
    private function _setClientRequestUrl()
    {
        if (\YunShop::request()->yz_redirect) {
            $yz_redirect = base64_decode(\YunShop::request()->yz_redirect);

            $redirect_url = $yz_redirect . '&t=' . time();
           /* if (preg_match('menu', $yz_redirect)) {
                $redirect_url = preg_replace('/menu/', 'redir_menu', $yz_redirect);
            } else {
                $redirect_url = preg_replace('/from=singlemessage/', 'redir_menu', $yz_redirect);
            }
            */

            Session::set('client_url', $redirect_url);
        } else {
            Session::set('client_url', '');
        }
    }

     /**
     * 获取客户端地址
     *
     * @return mixed
     */
    private function _getClientRequestUrl()
    {
        return Session::get('client_url');
    }


	 /**
     * 构造获取token的url连接
     * @param string $callback 支付宝服务器回跳的url，需要url编码
     * @return 返回构造好的url
     */
    private function __CreateOauthUrlForCode($appId, $callback, $state = 'info')
    {
        //return $this->url."?app_id=".$appId."&scope=auth_userinfo&redirect_uri=".$callback."&state=".$state;
        return $this->url."?app_id=".$appId."&scope=auth_user&redirect_uri=".$callback."&state=".$state;
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
