<?php

namespace app\Console\Commands;


use app\backend\modules\member\models\Member;
use app\common\models\AccountWechats;
use app\frontend\modules\member\models\MemberUniqueModel;
use Illuminate\Console\Command;

class WechatOpen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syn:wechatUnionid {uniacid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '微信开发平台同步Unionid';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $uniacid = $this->argument('uniacid');

        return $this->synRun($uniacid);
    }


    private function synRun($uniacid)
    {
        $member_info = Member::getQueueAllMembersInfo($uniacid);

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
            $time = time();
            $path = 'logs/' . $time . '_member_openid.log';
            $upgrade_path = 'logs/' . $time . '_upgrade_member_openid.log';
            $error_path = 'logs/' . $time . '_error_member_openid.log';

            collect($member_info)->map(function($item) use ($uniacid, $global_token, $path, $upgrade_path, $error_path) {
                try {
                    $item = $item->first();

                    if (!is_null($item->hasOneFans)) {
                        $UnionidInfo = MemberUniqueModel::getUnionidInfoByMemberId($item->hasOneFans->uid)->first();
                        $this->printLog($path, $item->hasOneFans->openid . '-' . $item->hasOneFans->uid);

                        if (is_null($UnionidInfo) && !empty($item->hasOneFans->openid)) {
                            \Log::debug('----start---', [$item->yzMember->member_id]);
                            $global_userinfo_url = $this->_getInfo($global_token['access_token'], $item->hasOneFans->openid);

                            $user_info = \Curl::to($global_userinfo_url)
                                ->asJsonResponse(true)
                                ->get();

                            if (isset($user_info['errcode'])) {
                                \Log::debug('----error---', [$item->yzMember->member_id]);
                                $this->printLog($error_path, $item->yzMember->member_id . '-' . $user_info['errmsg']);
                                return ['error' => 1, 'msg' => $user_info['errmsg']];
                            }

                            if (isset($user_info['unionid'])) {
                                MemberUniqueModel::insertData(array(
                                    'uniacid' => $uniacid,
                                    'unionid' => $user_info['unionid'],
                                    'member_id' => $item->hasOneFans->uid,
                                    'type' => 1
                                ));
                                \Log::debug('----insert---', [$item->yzMember->member_id]);
                                $this->printLog($upgrade_path, $item->hasOneFans->openid . '-' . $item->yzMember->member_id);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    throw $e;
                }
            });
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
