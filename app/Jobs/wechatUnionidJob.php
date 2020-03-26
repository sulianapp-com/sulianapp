<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/16
 * Time: 上午10:24
 */

namespace app\Jobs;


use app\backend\modules\member\models\Member;
use app\common\helpers\Cache;
use app\common\models\AccountWechats;
use app\frontend\modules\member\models\MemberUniqueModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class wechatUnionidJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $uniacid;
    private $member_info;

    public function __construct($uniacid, $member_info)
    {
        $this->uniacid = $uniacid;
        $this->member_info = $member_info->toArray();
    }

    public function handle()
    {
        \Log::debug('-----queque uniacid-----', $this->uniacid);
        return $this->synRun($this->uniacid, $this->member_info);
    }

    public function synRun($uniacid, $member_info)
    {
        //$member_info = Member::getQueueAllMembersInfo($uniacid);

        $account = AccountWechats::getAccountByUniacid($uniacid);
        $appId = $account->key;
        $appSecret = $account->secret;

        $global_access_token_url = $this->_getAccessToken($appId, $appSecret);

        $global_token = \Curl::to($global_access_token_url)
            ->asJsonResponse(true)
            ->get();

        return $this->requestWechatApi($uniacid, $member_info, $global_token);
    }

    private function requestWechatApi($uniacid, $member_info, $global_token)
    {
        if (!is_null($member_info)) {
            \Log::debug('------queque member_info-------');
            $time = time();
            $path = 'logs/' . $time . '_member_openid.log';
            $upgrade_path = 'logs/' . $time . '_upgrade_member_openid.log';
            $error_path = 'logs/' . $time . '_error_member_openid.log';

            collect($member_info)->map(function($item) use ($uniacid, $global_token, $path, $upgrade_path, $error_path) {
                \Log::debug('------queuqe coll-----', $item);

                try {
                    if (!is_null($item)) {
                        $UnionidInfo = MemberUniqueModel::getUnionidInfoByMemberId($uniacid, $item['uid'])->first();
                        $this->printLog($path, $item['openid'] . '-' . $item['uid']);

                        if (is_null($UnionidInfo) && !empty($item['openid'])) {
                            \Log::debug('----start---', [$item['uid']]);
                            $global_userinfo_url = $this->_getInfo($global_token['access_token'], $item['openid']);

                            $user_info = \Curl::to($global_userinfo_url)
                                ->asJsonResponse(true)
                                ->get();

                            if (isset($user_info['errcode'])) {
                                \Log::debug('----error---', [$item['uid']]);
                                $this->printLog($error_path, $item['uid'] . '-' . $user_info['errmsg']);
                                return ['error' => 1, 'msg' => $user_info['errmsg']];
                            }

                            if (isset($user_info['unionid'])) {
                                MemberUniqueModel::insertData(array(
                                    'uniacid' => $uniacid,
                                    'unionid' => $user_info['unionid'],
                                    'member_id' => $item['uid'],
                                    'type' => 1
                                ));
                                \Log::debug('----insert---', [$item['uid']]);
                                $this->printLog($upgrade_path, $item['openid'] . '-' . $item['uid']);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    throw $e;
                }
            });

            Cache::setUniacid($uniacid);

            if (Cache::has('queque_wechat_page')) {
                \Log::debug('----queque cache1----');
                $page = Cache::get('queque_wechat_page');
                $page++;
                \Log::debug('----queque cache1 page----', $page);
                Cache::put('queque_wechat_page', $page, 30);
            } else {
                \Log::debug('----queque cache2----');
                Cache::put('queque_wechat_page', 1, 30);
            }
        }
    }

    private function printLog($path, $openid)
    {
        file_put_contents(storage_path($path), $openid . "\r\n", FILE_APPEND);
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
     * 是否关注公众号
     *
     * @param $accesstoken
     * @param $openid
     * @return string
     */
    private function _getInfo($accesstoken, $openid)
    {
        return 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $accesstoken . '&openid=' . $openid;
    }
}