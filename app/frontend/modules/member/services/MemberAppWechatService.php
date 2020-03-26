<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/23
 * Time: 上午11:20
 */

namespace app\frontend\modules\member\services;

use app\frontend\modules\member\services\MemberService;

class MemberAppWechatService extends MemberService
{
    const LOGIN_TYPE    = 3;

    public function __construct()
    {}

    public function login()
    {
        $uniacid      = \YunShop::app()->uniacid;

        $appId        = \YunShop::app()->account['key'];
        $appSecret    = \YunShop::app()->account['secret'];
        $code         = \YunShop::request()->code;

        $tokenurl = $this->_getTokenUrl($appId, $appSecret, $code);

        if ($this->isLogged()) {
            return show_json(1, array('member_id'=> session('member_id')));
        }

        if (!empty($code)) {
            $resp     = @\Curl::to($tokenurl)->get();
            $token    = @json_decode($resp['content'], true);

            if (!empty($token) && is_array($token) && $token['errmsg'] == 'invalid code') {
                return show_json(0, array('msg'=>'请求错误'));
            }

            $userinfo_url = $this->_getUserInfoUrl($token['accesstoken'], $token['openid']);
            $user_info = @\Curl::to($userinfo_url)->get();

            if (is_array($user_info) && !empty($user_info['unionid'])) {
                $UnionidInfo = MemberUniqueModel::getUnionidInfo($uniacid, $user_info['unionid']);

                if ($UnionidInfo['unionid']) {
                    $types = explode('|',$UnionidInfo['type']);
                    $member_id = $UnionidInfo['member_id'];

                    if (!in_array(self::LOGIN_TYPE, $types)) {
                        //更新ims_yz_member_unique表
                        MemberUniqueModel::updateData(array(
                            'unique_id'=>$UnionidInfo['unique_id'],
                            'type' => $UnionidInfo['type'] . '|' . self::LOGIN_TYPE
                        ));

                        //添加yz_member_app_wechat表
                        MemberWechatModel::insertData(array(
                            'uniacid' => $uniacid,
                            'member_id' => $member_id,
                            'openid' => $user_info['openid'],
                            'nickname' => $user_info['nickname'],
                            'avatar' => $user_info['headimgurl'],
                            'gender' => $user_info['sex'],
                            'nationality' => $user_info['country'],
                            'resideprovince' => $user_info['province'] . '省',
                            'residecity' => $user_info['city'] . '市',
                            'created_at' => time()
                        ));
                    }
                } else {
                    $member_id = McMappingFansModel::getUId($uniacid, $token['openid']);

                    //添加ims_mc_member表
                    $member_id = MemberModel::insertData(array(
                        'uniacid' => $uniacid,
                        'groupid' => $user_info['unionid'],
                        'createtime' => TIMESTAMP,
                        'nickname' => $user_info['nickname'],
                        'avatar' => $user_info['headimgurl'],
                        'gender' => $user_info['sex'],
                        'nationality' => $user_info['country'],
                        'resideprovince' => $user_info['province'] . '省',
                        'residecity' => $user_info['city'] . '市'
                    ));

                    //添加ims_yz_member_unique表
                    MemberUniqueModel::insertData(array(
                        'uniacid' => $uniacid,
                        'unionid' => $token['unionid'],
                        'member_id' => $member_id,
                        'type' => self::LOGIN_TYPE
                    ));

                    //添加yz_member_app_wechat表
                    MemberWechatModel::insertData(array(
                        'uniacid' => $uniacid,
                        'member_id' => $member_id,
                        'openid' => $user_info['openid'],
                        'nickname' => $user_info['nickname'],
                        'avatar' => $user_info['headimgurl'],
                        'gender' => $user_info['sex'],
                        'nationality' => $user_info['country'],
                        'resideprovince' => $user_info['province'] . '省',
                        'residecity' => $user_info['city'] . '市',
                        'created_at' => time()
                    ));

                    session()->put('member_id',$member_id);
                }
            } else {
                show_json(0, array('msg'=> '请求错误'));
            }
        } else {
            show_json(0, array('msg'=> '请求错误'));
        }

        show_json(1, array('member_id', session('member_id')));
    }

    private function _getTokenUrl($appId, $appSecret, $code)
    {
        return "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appId}&secret={$appSecret}&code={$code}&grant_type=authorization_code";
    }

    private function _getUserInfoUrl($accesstoken, $openid)
    {
        return "https://api.weixin.qq.com/sns/userinfo?access_token={$accesstoken}&openid={$openid}&lang=zh_CN";
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