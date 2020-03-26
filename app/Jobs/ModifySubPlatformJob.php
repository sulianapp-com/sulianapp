<?php

namespace app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Ixudra\Curl\Facades\Curl;

class ModifySubPlatformJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;


    protected $platform;
    protected $data;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tripartite_provider,$datas)
    {
        //子平台信息修改
        $this->platform = $tripartite_provider;
        $this->data = $datas;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->platform as $item){
            \Log::debug('进入handle',$item);
            \Log::debug('打印',[$item['domain'],$item['platform_uniacid']]);
            $url = "{$item['domain']}/addons/yun_shop/api.php?i={$item['platform_uniacid']}&mid=0&type=5&shop_id=null&route=plugin.provider-platform.api.tripartiteProviderWithdrawal.SubplatformInfo.store";
            \Log::debug('进入$url',$url);
            // 提交推送请求
            $response = Curl::to($url)->withData(['data' => json_encode($this->data,1)])->asJsonResponse(true)->post();
            if ($response['result'] != 1) {
                \Log::debug('域名为'.$item['domain'].'公众ID为'.$item['platform_uniacid'].'的平台的域名或公众号ID有误');
            }
        }
    }
}
