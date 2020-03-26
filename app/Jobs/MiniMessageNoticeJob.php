<?php

namespace app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class MiniMessageNoticeJob implements  ShouldQueue
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

    protected $noticeModel;
    protected $templateId;
    protected $noticeData;
    protected $openId;
    protected $url;
    protected $formId;
    protected $app_id;
    protected $app_secret;
    protected $get_token_url;

    /**
     * Create a new job instance.
     *
     *
     */
    public function __construct($options, $templateId, $noticeData, $openId, $url,$formId)
    {
        $this->app_id = $options['app_id'];
        $this->app_secret = $options['secret'];
        $this->templateId = $templateId;
        $this->noticeData = $noticeData;
        $this->openId = $openId;
        $this->url = $url?:'pages/index/index';
        $this->formId = $formId;
        $this->get_token_url = 'https://api.weixin.qq.com/cgi-bin/token?'
            .'grant_type=client_credential&appid=%s&secret=%s';
//        "https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=$code&grant_type=authorization_code"
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        if ($this->attempts() > 2) {
            \Log::info('消息通知测试，执行大于两次终止');
            return true;
        }
        $this->sendTemplate();
        //$this->noticeModel->uses($this->templateId)->andData($this->noticeData)->andReceiver($this->openId)->andUrl($this->url)->send();
        return true;
    }

    public function sendTemplate($method_msg = 'sendTemplate'){
        $opUrl = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=%s";
        $rawPost = [
                    'touser' => $this->openId ,
                    'template_id' => $this->templateId,
                    'page'=>$this->url,
                    'form_id' => $this->formId,
                    'data' =>$this->noticeData
                ];
        \Log::debug('=================111111参数1111111================');
        \Log::debug($rawPost);
        $this->opTemplateData($opUrl,$rawPost,$method_msg);
    }
    /**
     * 提取公共方法 获取模板数据
     * @param string $opUrl
     * @param array $rawPost
     * @param string $method
     */
    public function opTemplateData($opUrl = '',$rawPost = [],$method = ''){
        $access_token = self::opGetAccessToken();
        \Log::debug('=================22222 access_token 2222================');
        \Log::debug($access_token);
        if(!$access_token){
            $this->return_err('获取 access_token 时异常，微信内部错误');
        }else{
            $templateUrl = sprintf($opUrl,$access_token);
            $listRes = self::curl_post($templateUrl,$rawPost);
            \Log::debug($templateUrl);
            \Log::debug($rawPost);
            \Log::debug('=================33333333发送返回值333333333================');
            \Log::debug($listRes);
            $wxResult = json_decode($listRes,true);
            if($wxResult['errcode']){
                return ($method.' - Failed!:'.$wxResult);
            }else{
                return $wxResult;
            }
        }
    }
    /**
     * 提取公共方法 - 获取 AccessToken
     * @return bool
     */
    public function opGetAccessToken(){
        $get_token_url = sprintf($this->get_token_url, $this->app_id,$this->app_secret);
        $result = self::curl_get($get_token_url);
        $wxResult = json_decode($result,true);
        if(empty($wxResult)){
            return false;
        }else{
            $access_token = $wxResult['access_token'];
            return $access_token;
        }
    }

    /**
     * @param string $url get请求地址
     * @param int $httpCode 返回状态码
     * @return mixed
     */
    protected function curl_get($url,&$httpCode = 0){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

        //不做证书校验，部署在linux环境下请改位true
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,true);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);
        $file_contents = curl_exec($ch);
        $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $file_contents;
    }
    /**
     * PHP 处理 post数据请求
     * @param $url 请求地址
     * @param array $params 参数数组
     * @return mixed
     */
    protected function curl_post($url,array $params = array()){
        //TODO 转化为 json 数据
        $data_string = json_encode($params);
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data_string);
        curl_setopt($ch,CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json'
            )
        );
        $data = curl_exec($ch);
        curl_close($ch);
        return ($data);
    }
}
