<?php
/**
 * Created by PhpStorm.
 * User: yangming
 * Date: 17/8/2
 * Time: 上午11:20
 */

namespace app\frontend\modules\member\services;

use app\common\helpers\Client;
use app\common\helpers\Url;
use app\common\services\Session;
use app\frontend\models\Member;
use app\frontend\modules\member\models\McMappingFansModel;
use app\frontend\modules\member\models\MemberWechatModel;
use app\frontend\modules\member\models\MemberUniqueModel;
use app\frontend\modules\member\models\MemberModel;
use Crypt;
use app\common\models\MemberShopInfo;

class MemberAppYdbService extends MemberService
{
    const LOGIN_TYPE = 7;

    public function __construct()
    {

    }

    public function login()
    {
        $uniacid  = \YunShop::app()->uniacid;
        $mobile   = \YunShop::request()->mobile;
        $password = \YunShop::request()->password;
        $uuid     = trim($_REQUEST['uuid']);

        if (!empty($mobile) && !empty($password)) {
            if (\Request::isMethod('post') && MemberService::validate($mobile, $password)) {
                $has_mobile = MemberModel::checkMobile($uniacid, $mobile);

                if (!empty($has_mobile)) {
                    $password = md5($password . $has_mobile->salt);

                    $member_info = MemberModel::getUserInfo($uniacid, $mobile, $password)->first();

                } else {
                    return show_json(7, "用户不存在");
                }

                if (!empty($member_info)) {
                    $member_info = $member_info->toArray();

                    //生成分销关系链
                    Member::createRealtion($member_info['uid']);

                    $this->save($member_info, $uniacid);

                    $yz_member = MemberShopInfo::getMemberShopInfo($member_info['uid']);

                    if (!empty($yz_member)) {
                        $yz_member = $yz_member->toArray();

                        $data = MemberModel::userData($member_info, $yz_member);
                    } else {
                        $data = $member_info;
                    }

                    if (!empty($uuid)) {
                        $wechat_member = MemberWechatModel::getFansById($member_info['uid']);
                        if (!empty($wechat_member)) {
                            MemberWechatModel::updateData($member_info['uid'], array('uuid' => $uuid));
                        } else {
                            MemberWechatModel::insertData(array(
                                'uniacid'   => $uniacid,
                                'member_id' => $member_info['uid'],
                                'openid'    => $member_info['mobile'],
                                'nickname'  => $member_info['nickname'],
                                'gender'    => $member_info['gender'],
                                'avatar'    => $member_info['avatar'],
                                'province'  => $member_info['resideprovince'],
                                'city'      => $member_info['residecity'],
                                'country'   => $member_info['nationality'],
                                'uuid'      => $uuid
                            ));
                        }
                    }

                    return show_json(1, $data);
                } else {
                    return show_json(6, "手机号或密码错误");
                }
            } else {
                return show_json(6, "手机号或密码错误");
            }
        } else {
            $para = \YunShop::request();
            \Log::debug('获取用户信息：', print_r($para, 1));
            $member = MemberWechatModel::getUserInfo($para['openid']);
            if($member){
                Session::set('member_id', $member['member_id']);
                $this->redirect_link($para['openid']);
            }

            if ($para['openid'] && $para['token']) {
                $this->app_get_userinfo($para['token'], $para['openid'], $uuid);
            } elseif ($para['openid']) {
                $this->redirect_link($para['openid']);
            }

            if ($para['apptoken']) {
                $openid = Crypt::decrypt($para['apptoken']);
                $member = MemberWechatModel::getUserInfo($openid);
                if (!$member) {
                    return show_json(3, '登录失败，请重试');
                }
                Session::set('member_id', $member['member_id']);

                return show_json(1, $member->toArray());
            }
        }
    }

    /**
     * app获取用户信息并存储
     *
     * @param $token
     * @param $openid
     */
    public function app_get_userinfo($token, $openid, $uuid)
    {
        //通过接口获取用户信息
        $url       = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $token . '&openid=' . $openid;
        $user_info = \Curl::to($url)
            ->asJsonResponse(true)
            ->get();

        if (!empty($uuid)) {
            $user_info['uuid'] = $uuid;
        }

        if (!empty($user_info) && !empty($user_info['unionid'])) {
            $this->memberLogin($user_info);
            exit('success');
        } else {
            exit('fail');
        }
    }

    /**
     * app登录跳转到前端
     *
     * @param $openid
     */
    public function redirect_link($openid)
    {
        if (!$openid) {
            $url = Url::absoluteApp('login');
        } else {
            $apptoken = Crypt::encrypt($openid);
            $url      = Url::absoluteApp('login_validate', ["apptoken" => $apptoken]);
        }

        redirect($url)->send();
        exit();
    }

    public function updateMemberInfo($member_id, $userinfo)
    {
        parent::updateMemberInfo($member_id, $userinfo);

        $record = array(
            'openid'   => $userinfo['openid'],
            'nickname' => stripslashes($userinfo['nickname']),
            'uuid'     => $userinfo['uuid']
        );
        MemberWechatModel::updateData($member_id, $record);
    }

    public function addMemberInfo($uniacid, $userinfo)
    {
        $uid = parent::addMemberInfo($uniacid, $userinfo);

        $this->addFansMember($uid, $uniacid, $userinfo);

        return $uid;
    }

    public function addMcMemberFans($uid, $uniacid, $userinfo)
    {
        McMappingFansModel::insertData($userinfo, array(
            'uid'     => $uid,
            'acid'    => $uniacid,
            'uniacid' => $uniacid,
            'salt'    => Client::random(8),
        ));
    }

    public function addFansMember($uid, $uniacid, $userinfo)
    {
        $user = MemberWechatModel::getUserInfo_memberid($uid);
        if (!empty($user)) {
            $this->updateMemberInfo($uid, $userinfo);
        } else {
            MemberWechatModel::insertData(array(
                'uniacid'   => $uniacid,
                'member_id' => $uid,
                'openid'    => $userinfo['openid'],
                'nickname'  => $userinfo['nickname'],
                'avatar'    => $userinfo['headimgurl'],
                'gender'    => $userinfo['sex'],
                'province'  => '',
                'country'   => '',
                'city'      => '',
                'uuid'      => $userinfo['uuid']
            ));
        }
    }

    public function getFansModel($openid)
    {
        return McMappingFansModel::getUId($openid);
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
            'uniacid'   => $uniacid,
            'unionid'   => $unionid,
            'member_id' => $member_id,
            'type'      => self::LOGIN_TYPE
        ));
    }

    /**
     * 验证登录状态
     *
     * @return bool
     */
    public function checkLogged($login = null)
    {
        $uuid       = trim($_REQUEST['uuid']);

        if (!MemberService::isLogged()) {
            $member = MemberWechatModel::getUserInfoByUuid($uuid);

            if (!$member) {
                return false;
            }

            Session::set('member_id', $member['member_id']);
        }

        return true;
    }
}