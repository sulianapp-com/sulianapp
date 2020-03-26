<?php

namespace app\Jobs;

use app\common\models\AccountWechats;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use EasyWeChat\Foundation\Application;

class MessageNoticeJob implements  ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    protected $templateId;
    protected $noticeData;
    protected $openId;
    protected $url;
    protected $uniacid;

    /**
     * MessageNoticeJob constructor.
     * @param $templateId
     * @param $noticeData
     * @param $openId
     * @param $url
     * @param $uniacid
     */
    public function __construct($templateId, $noticeData, $openId, $url)
    {

        $this->templateId = $templateId;
        $this->noticeData = $noticeData;
        $this->openId = $openId;
        $this->url = $url;
        $this->uniacid = \YunShop::app()->uniacid;
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        if ($this->attempts() > 1) {
            \Log::info('消息通知测试，执行大于两次终止');
            return true;
        }
        $res = AccountWechats::getAccountByUniacid($this->uniacid);
        $options = [
            'app_id' => $res['key'],
            'secret' => $res['secret'],
        ];
        $app = new Application($options);
        $app = $app->notice;
        $app->uses($this->templateId)->andData($this->noticeData)->andReceiver($this->openId)->andUrl($this->url)->send();
        return true;
    }
}
