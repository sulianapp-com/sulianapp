<?php
namespace iscms\Alisms;

use Illuminate\Support\Facades\Auth;
use iscms\AlismsSdk\AlibabaAliqinFcSmsNumSendRequest;
use iscms\AlismsSdk\TopClient;

class SendsmsPusher implements SendSmsApi
{

    private $TopClient,$name,$content,$phone,$code;

    /**
     * 注入框架
     * SendsmsPusher constructor.
     * @param TopClient $topClient
     */
    public function __construct(TopClient $topClient)
    {
        $this->TopClient = $topClient;
        $this->TopClient->appkey = config('alisms.KEY');
        $this->TopClient->secretKey = config('alisms.SECRETKEY');
        $this->TopClient->format = "json";
        $this->TopClient->simplify = true;
    }
    /**
     * @param $phone 接收人手机号码
     * @param $name  短信签名,可以在阿里大鱼的管理中心看到
     * @param $content 内容 应该以json格式传入"{'code':'1234','product':'alidayu'}"对应模板中的字符
     * @param $code 短信模板编号 exp:SMS_4955428 在阿里大鱼里找
     * @return
     */
    public function send($phone,$name, $content, $code)
    {
        $send_data=(object)(
            ['phone'=>$phone,
            'name'=>$name,
            'content'=>$content,
            'code'=>$code
            ]
        );
        return $this->push($send_data);
    }

    /**
     * 推送组合机
     * @param $data
     * @return \Iscloudx\AlibigfishSdk\ResultSet|mixed|\SimpleXMLElement
     */
    private function push($data)
    {
        $req = new AlibabaAliqinFcSmsNumSendRequest();

        if (Auth::check())
        {
            $req->setExtend(Auth::User()->id);
        }else{
            $req->setExtend(0);
        }
        $req->setSmsType("normal");
        $req->setSmsFreeSignName($data->name);
        $req->setSmsParam($data->content);
        $req->setRecNum($data->phone);//参数为用户的手机号码
        $req->setSmsTemplateCode($data->code);
        $resp = $this->TopClient->execute($req);
        return $resp;
    }
}