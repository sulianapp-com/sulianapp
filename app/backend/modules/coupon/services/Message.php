<?php

namespace app\backend\modules\coupon\services;

use app\common\services\MessageService;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Text;

class Message
{
    //默认使用微信"客服消息"通知, 对于超过 48 小时未和平台互动的用户, 使用"模板消息"通知
    public static function message($data, $templateId = null, $uid)
    {
        self::sendTemplateNotice($uid, $templateId, $data);
//        try {
//            self::sendNotice($openid, $data);
//        } catch (\Exception $e) {
//            try {
//                self::sendTemplateNotice($uid, $templateId, $data);
//            } catch (\Exception $e) {
//                //
//            }
//        }
    }

    //发送微信"客服消息"
    /*
     * $notice可以是微信文本回复或者微信图文回复
     * 文本: $data = new Text(['content' => 'Hello']);
     * 图文:
     * $data = new News([
                    'title' => 'your_title',
                    'image' => 'your_image',
                    'description' => 'your_description',
                    'url' => 'your_url',
                ]);
     */
    public static function sendNotice($openid, $data)
    {
        $app = app('wechat');
        if (array_key_exists('content', $data)) {
            $data = new Text($data); //发送文本消息
        } else {
            $data = new News($data); //发送图文消息
        }
        $app->staff->message($data)->to($openid)->send();
    }

    //发送微信"模板消息"
    /*
     * * 如果只使用模板消息 -- "业务处理通知":
     * $data = [
     *      'first' => 'your_title',
            'keyword1' => 'your_description',
            'keyword2' => 'your_description',
            'url' => 'your_url',
     * ]
     * 如何需要和"客服消息"共用数据 (注意模板消息中无法发送图片):
     * $data = [
                    'title' => 'your_title',
                    'image' => 'your_image',
                    'description' => 'your_description',
                    'url' => 'your_url',
                ];
     */
    public static function sendTemplateNotice($uid, $templateId, $data)
    {
//        $app = app('wechat');
//        $notice = $app->notice;

        $url = $data['url'];
        if (array_key_exists('description', $data)) { //如果需要和"客服消息"共用数据
            $data = [
                'first' => '您好',
                'keyword1' => $data['title'],
                'keyword2' => $data['description'],
                'remark' => '',
            ];
        }
        MessageService::notice($templateId, $data, $uid);
//        $notice->to($openid)->uses($templateId)->andUrl($url)->data($data)->send();
    }
}