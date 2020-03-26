<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/22
 * Time: 下午4:12
 */

namespace app\frontend\modules\member\services;

use app\common\models\MemberShopInfo;
use app\frontend\models\Member;
use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\member\models\MemberModel;

class MemberMobileService extends MemberService
{
    public function login()
    {
        $mobile   = \YunShop::request()->mobile;
        $password = \YunShop::request()->password;
        $uniacid  = \YunShop::app()->uniacid;

        if (\Request::isMethod('post')
                                  && MemberService::validate($mobile, $password)) {
            $has_mobile = MemberModel::checkMobile($uniacid, $mobile);
            if (!empty($has_mobile)) {
                $password = md5($password. $has_mobile->salt);

                $member_info = MemberModel::getUserInfo($uniacid, $mobile, $password)->first();

            } else {
                return show_json(7, "用户不存在");
            }

            if(!empty($member_info)){
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

                return show_json(1, $data);
            } {
                return show_json(6, "手机号或密码错误");
            }
        } else {
            return show_json(6,"手机号或密码错误");
        }

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