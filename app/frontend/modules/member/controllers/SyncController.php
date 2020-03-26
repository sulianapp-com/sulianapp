<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/22
 * Time: 上午11:56
 */

namespace app\frontend\modules\member\controllers;


use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\services\Session;
use app\frontend\modules\member\models\MemberModel;
use app\frontend\modules\member\models\SubMemberModel;

class SyncController extends BaseController
{
    public function index()
    {
        $token = \YunShop::request()->token;
        $type  = \YunShop::request()->type ?: 7;

        $servceSet = \Setting::get('plugins.sync-land');

        if (! $servceSet['encryption']){
            return $this->errorJson('加密key为空');
        }

        $key   = $servceSet['encryption'];

        if ($token) {
            $decrypt = $this->decrypt($key, $token);

            $uid     = $decrypt ? explode('_', $decrypt) : 0;
            $uid     = $uid ? $uid[0] : 0;
            $member  = SubMemberModel::getMemberShopInfo($uid);

            if (!$member) {
                $member = MemberModel::getMemberById($uid);

                if (!$member) {
                    return $this->errorJson('会员不存在');
                }

                SubMemberModel::insertData(array(
                    'member_id'    => $member->uid,
                    'uniacid'      => $member->uniacid,
                    'parent_id'    => 0,
                    'group_id'     => $member->groupid,
                    'level_id'     => 0,
                    'pay_password' => '',
                    'salt'         => '',
                    'invite_code'  => MemberModel::generateInviteCode(),
                    'yz_openid'    => 0,
                ));
            }

            Session::set('member_id', $uid);
            $url = yzAppFullUrl('home', ['i'=> $member->uniacid, 'type' => $type]);

            redirect($url)->send();
        }

        return $this->errorJson('参数有误');
    }

    private function decrypt($key, $plain_text)
    {
        $decrypted = openssl_decrypt(hex2bin($plain_text), 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        return $decrypted;
    }
}