<?php
namespace app\common\services\notice;

use app\backend\modules\goods\models\Brand;
use app\backend\modules\goods\services\BrandService;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Illuminate\Support\Facades\DB;
use app\common\models\MemberMiniAppModel;
use app\common\helpers\Cache;
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/27
 * Time: 上午9:17
 */
class SmallProgramNotice
{
    protected $app_id;
    protected $app_secret;
    protected $get_token_url;

    public function __construct()
    {
        /**
         * 请在此处填写你的小程序 APPID和秘钥
         */
        $set = \Setting::get('plugin.min_app');
        $getTokenUrl = "https://api.weixin.qq.com/cgi-bin/token?"; //获取token的url
        $WXappid     =  $set['key']; //APPID
        $WXsecret    = $set['secret']; //secret

        $this->app_id =  $WXappid;      //"wxbe88683bd339aaf5";
        $this->app_secret = $WXsecret;   //"fcf189d2a18002a463e7b675cea86c87";
        $this->get_token_url = 'https://api.weixin.qq.com/cgi-bin/token?'
            .'grant_type=client_credential&appid=%s&secret=%s';
    }

    /**
     * 微信获取 AccessToken
     */
    public function getAccessToken(){
        $access_token = Cache::remember('token', 120, function (){
            $access_token = $this->opGetAccessToken();
            return $access_token;
        });

        if(!$access_token){
            $this->return_err('获取access_token时异常，微信内部错误');
        }else{
            $this->return_data(['access_token'=>$access_token]);
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
     * 获取小程序模板库标题列表
     * TODO 没必要使用，小程序账号后台可以视图查看
     */
    public function getAllTemplateList($offset){
        $opUrl = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/library/list?'
            .'access_token=%s';
        $rawPost = ['count'=>20,'offset'=>$offset];
      $date = self::opTemplateData($opUrl,$rawPost,'getAllTemplateList');
      return $date;
    }

    /**
     * 获取模板库某个模板标题下关键词库
     * TODO 没必要使用，小程序账号后台可以视图查看
     */
    public function getTemplateKey($key){
        $opUrl = "https://api.weixin.qq.com/cgi-bin/wxopen/template/library/get?access_token=%s";
        $rawPost = ['id'=>$key];
        $key_liset = $this->opTemplateData($opUrl,$rawPost,'getTemplateKey');
        return $key_liset;
    }
    /**
     * 删除模板
     * TODO 没必要使用，小程序账号后台可以视图查看
     */
    public function deleteTemplate($key){
        $opUrl = "https://api.weixin.qq.com/cgi-bin/wxopen/template/del?access_token=%s";
        $rawPost = ['template_id'=>$key];
        $key_liset = $this->opTemplateData($opUrl,$rawPost,'getTemplateKey');
        return $key_liset;
    }

    /**
     * 获取帐号下已存在的模板列表
     * TODO 没必要使用，小程序账号后台可以视图查看
     */
    public function getExistTemplateList(){
        $opUrl = "https://api.weixin.qq.com/cgi-bin/wxopen/template/list?access_token=%s";
        $rawPost = ['count'=>20,'offset'=>0];
        return $this->opTemplateData($opUrl,$rawPost,'getExistTemplateList');
    }


    public function getAddTemplate($id,$keyword){
        $opUrl = "https://api.weixin.qq.com/cgi-bin/wxopen/template/add?access_token=%s";
        $rawPost = ['id'=>$id,'keyword_id_list'=>$keyword];

        return $this->opTemplateData($opUrl,$rawPost,'getAddTemplate');
    }

    /**
     * 提取公共方法 获取模板数据
     * @param string $opUrl
     * @param array $rawPost
     * @param string $method
     */
    public function opTemplateData($opUrl = '',$rawPost = [],$method = ''){
        $access_token = self::opGetAccessToken();
        if(!$access_token){
            return '获取 access_token 时异常，微信内部错误';
        }else{
            $templateUrl = sprintf($opUrl,$access_token);
            $listRes = self::curl_post($templateUrl,$rawPost);
            $wxResult = json_decode($listRes,true);
            if($wxResult['errcode']){
                return ($method.' - Failed!:'.$wxResult);
            }else{
                return $wxResult;
            }
        }
    }
    public function getOpenid($memberId){
        return MemberMiniAppModel::getFansById($memberId)->openid;
    }

//    public function sendTemplatePaySuccess(\Illuminate\Http\Request $request){
//        if ($request->isMethod('post')){
//                $openId =$this->getOpenid(\YunShop::request()['member']);//接受人open_id
//                $url = \YunShop::request()['url'];    //跳转路径
//                $form_id = \YunShop::request()['form_id'];  //类型
//                /*-------------------此为项目的特定业务处理---------------------------*/
//                $order_sn = '';
//                $orderModel = new OrderModel();
//                $sendTemplateData = $orderModel->getSendTemplateData($order_sn);
//                /*-----------以上数据 $sendTemplateData 可根据自己的实际业务进行获取-----*/
//                $rawPost = [
//                    'touser' => $openId ,
//                    'template_id' => 'yASr1SdzgV7_gRzKgqYI3t7um-3pIGXrpCcHUHVIJz4',
//                    'page'=>$url,
//                    'form_id' => $form_id,
//                    'data' => [
//                        'keyword1' => ['value' => $sendTemplateData['order_sn']],
//                        'keyword2' => ['value' => $sendTemplateData['pay_time']],
//                        'keyword3' => ['value' => $sendTemplateData['goodsMsg']],
//                        'keyword4' => ['value' => $sendTemplateData['order_amount']],
//                        'keyword5' => ['value' => $sendTemplateData['addressMsg']],
//                        'keyword6' => ['value' => $sendTemplateData['tipMsg']],
//                    ]
//                ];
//
//            $this->sendTemplate($rawPost,'sendTemplatePaySuccess');
//        }else{
//            return $this->return_err('Sorry,请求不合法');
//        }
//
//    }



    /**
     * 错误返回提示
     * @param string $errMsg 错误信息
     * @param string $errMsg
     * @param array $data
     */
    protected function return_err($errMsg = 'fail',$data = array())
    {
        exit(json_encode(array('status' => 0, 'result' => $errMsg, 'data' => $data)));
    }


    /**
     * 正确返回
     * @param    array $data 要返回的数组
     * @return  json的数据
     */
    protected function return_data($data = array())
    {
        exit(json_encode(array('status' => 1, 'result' => 'success', 'data' => $data)));
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
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);
        $file_contents = curl_exec($ch);
        $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $file_contents;
    }
}