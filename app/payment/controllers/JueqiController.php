<?php


namespace app\payment\controllers;

use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\models\AccountWechats;
use app\common\services\Pay;
use app\payment\PaymentController;
use Yunshop\CloudPay\services\CloudPayNotifyService;


class JueqiController extends PaymentController
{

    private $data = [];

    private $orderSn = '';
    public function preAction()
    {
        parent::preAction();


        if (empty(\YunShop::app()->uniacid)) {
            $res =  $this->getPost();
            if($res === true){
                \Setting::$uniqueAccountId = \YunShop::app()->uniacid = request()->input('uniacid');
                AccountWechats::setConfig(AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid));
            }

        }
    }

    /**
     * JSAPI 回调
     */
    public function notifyUrl()
    {
        $this->log($this->data);
        \Log::info('-------this->data---------',$this->data);
        // 微信
        if ($this->data['source'] == 1 && $this->data['state'] ==2) {
            \Log::info('------验证成功-----');
            $data = [
                'total_fee'    => floatval($this->data['truemoney']),
                'out_trade_no' => $this->orderSn,
                'trade_no'     => $this->data['oid'],
                'unit'         => 'yuan',
                'pay_type'     => '云支付',
                'pay_type_id'     => 33
            ];
            $this->payResutl($data);
            \Log::info('----结束----');
            echo "success";
        } else {
            echo "fail";
        }
    }

    /**
     * 小程序回调
     */
    public function notifyMiniAppUrl()
    {
        //todo 验证签名在此方法下进行
//        $data = $this->getPost();
        $this->log($this->data);
//        $this->getSignResult($data['paySign']);
        if (($this->data['source'] == 1 || $this->data['source'] == 2 || $this->data['source'] == 3) && $this->data['state'] ==2) {
            if($this->getQuery() !== true){
                echo 'fail';exit();
            }
            \Log::debug('------验证成功-----');
            $data = [
                'total_fee'    => floatval($this->data['truemoney']),
                'out_trade_no' => $this->orderSn,
                'trade_no'     => $this->data['oid'],
                'unit'         => 'yuan',
                'pay_type'     => '云支付',
                'pay_type_id'     => 33
            ];

            $this->payResutl($data);
            \Log::debug('----结束----');
            echo "success";
        } else {
            echo "fail";
        }
    }

    public function getPost()
    {
        //支付回调接口（jsapi支付）
        //获取xml
        $xmlData = file_get_contents('php://input');
        libxml_disable_entity_loader(true);
        //转换成数组
        $rsdata = json_decode(json_encode(simplexml_load_string($xmlData, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        \Log::info($rsdata);
        ksort($rsdata);
        if(empty($rsdata)){
            return true;
        }
        $this->data = $rsdata;
        if($this->data){
            $data = explode(':', $this->data['olid']);
            $this->orderSn = $data['0'];
            \Log::info($data['0']);
            \Log::info($data['1']);
            \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $data['1'];
        }
        AccountWechats::setConfig(AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid));

        $buff = '';
        foreach ($rsdata as $k => $v){
            if($k != 'sign'){
                $buff .= $k . '=' . $v . '&';
            }
        }

        $stringSignTemp = $buff .'key='.\Setting::get('plugin.jueqi_pay_set.key'); //支付秘钥
        \Log::info('-------key---------',\Setting::get('plugin.jueqi_pay_set.key'));
        \Log::info('-------stringSignTemp---------',$stringSignTemp);
        $sign = strtoupper(md5($stringSignTemp));//生成签名
        \Log::info('-------sign---------',$sign);
        //验证签名
        if($sign == $rsdata['sign']){
            return $rsdata;
        }else{
            \Log::info("data-no:".$xmlData );
            exit('FAIL');
        }
    }

    /**
     * 成功跳转页面
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function returnUrl()
    {
        redirect(Url::absoluteApp('home'))->send();
    }

    public function frontUrl()
    {
        $trade = \Setting::get('shop.trade');

        if (!is_null($trade) && isset($trade['redirect_url']) && !empty($trade['redirect_url'])) {
            return redirect($trade['redirect_url'])->send();
        }

        if (0 == $_GET['state'] && $_GET['errorDetail'] == '成功') {
            redirect(Url::absoluteApp('member', ['i' => $_GET['attach']]))->send();
        } else {
            redirect(Url::absoluteApp('member', ['i' => $_GET['attach']]))->send();
        }
    }


    /**
     * 支付日志
     *
     * @param $post
     */
    public function log($data)
    {
        //访问记录
        Pay::payAccessLog();
        //保存响应数据
        Pay::payResponseDataLog($data['olid'], '云支付', json_encode($data));
    }

    private function getQuery()
    {
       //订单查询接口
        $stringSignTemp = 'olid='.$this->data['olid'].'&key='.\Setting::get('plugin.jueqi_pay_set.key'); //支付秘钥
        $sign = strtoupper(md5($stringSignTemp));//生成签名

        $post_data['olid'] = $this->data['olid']; //唯一标识
        $post_data['sign'] = $sign; //签名
        $url = \Setting::get('plugin.jueqi_pay_set.order_query');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        //获取xml
        libxml_disable_entity_loader(true);
        $rsdata = json_decode(json_encode(simplexml_load_string($output, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        if($rsdata['state'] == 2){
            return true;
        }else{
            \Log::debug($rsdata['errordesc']);
        }
    }
}