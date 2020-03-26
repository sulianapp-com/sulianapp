<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/6/12
 * Time: 下午5:51
 */

namespace app\common\services\wechat;

use app\common\facades\Setting;

class Notice extends \EasyWeChat\Notice\Notice
{
    public function send($data = [])
    {
        if(Setting::get('shop.notice.toggle') == false){
            return false;
        }
        return parent::send();
    }

    protected function checkAndThrow(array $contents)
    {
        if (isset($contents['errcode']) && 0 !== $contents['errcode']) {
            if (empty($contents['errmsg'])) {
                $contents['errmsg'] = 'Unknown';
            }
            \Log::error('消息推送出错', $contents);
        }
    }
}