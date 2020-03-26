<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/23
 * Time: 上午10:53
 */

/**
 * 公众号登录表
 */
namespace app\frontend\modules\member\models;

use app\common\models\McMappingFans;

class McMappingFansModel extends McMappingFans
{
    public $timestamps = false;

    protected $guarded = [];

    protected $fillable = ['openid','uid','acid','uniacid', 'salt', 'updatetime', 'nickname', 'follow', 'followtime', 'unfollowtime', 'tag'];

    protected $attributes = ['unionid' => '', 'groupid' => 0];


    /*public function getOauthUserInfo()
    {
        return mc_oauth_userinfo();
    }*/

    /**
     * 获取粉丝uid
     *
     * @param $openid
     * @return mixed
     */
    public static function getUId($openid)
    {
        return self::select('uid')
            ->uniacid()
            ->where('openid', $openid)
            ->first();
    }

    /**
     * 添加数据
     *
     * @param $data
     */
    public static function insertData($userinfo, $data)
    {
        if (isset($userinfo['subscribe']) && 1 == $userinfo['subscribe']) {
            $subscribe = 1;
            $followtime = explode(',', rtrim($userinfo['subscribe_time'],','));
            $count      = count($followtime);
            $follow_time = $followtime[$count-1];
        } else {
            $subscribe = 0;
            $follow_time = time();
        }


        $fans_model = new McMappingFansModel();

        $fans_model->openid = $userinfo['openid'];
        $fans_model->unionid = !empty($userinfo['unionid']) ? $userinfo['unionid'] : '';
        $fans_model->uid = $data['uid'];
        $fans_model->acid = $data['uniacid'];
        $fans_model->uniacid = $data['uniacid'];
        $fans_model->salt = $data['salt'];
        $fans_model->updatetime = time();
        $fans_model->nickname = stripslashes($userinfo['nickname']);
        $fans_model->follow = $subscribe;
        $fans_model->followtime = $follow_time;
        $fans_model->unfollowtime = 0;
        $fans_model->tag = '';//小程序数据过长无法添加 base64_encode(serialize($userinfo));

        if ($fans_model->save()) {
            return $fans_model->uid;
        } else {
            return false;
        }
    }

    /**
     * 更新数据
     *
     * @param $uid
     * @param $data
     */
    public static function updateData($uid, $data)
    {
        self::uniacid()
            ->where('uid', $uid)
            ->update($data);
    }

    public static function updateDataById($id, $data)
    {
        self::uniacid()
            ->where('fanid', $id)
            ->update($data);
    }

    /**
     * 获取粉丝数据
     *
     * @param $openid
     * @return mixed
     */
    public static function getFansData($openid)
    {
        return self::select('fanid', 'uid','follow')
            ->uniacid()
            ->where('openid', $openid)
            ->first();
    }
}