<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/10/23
 * Time: 11:23
 */

namespace app\common\services\wechat;

use GuzzleHttp\Client;

class WxQrCode
{
 
    public $param;
    public $page;
    public $auto_color = false;
    public $width = 430;//280-1280
    public $line_color = '';
    public $is_hyaline = false;

    public  function mergeQrImage()
    {
        $res = $this->getWxacode();

        if ($res === false) {return ;}

        \Log::debug('------------小程序二维码-----------', $res);

        return $res;
    }

    public function setParam($data)
    {
        $this->param = $data['param'];
        $this->page = $data['page'];
        if ($data['auto_color']) $this->auto_color = $data['auto_color'];
        if ($data['width']) $this->width = $data['width'];
        if ($data['line_color']) $this->line_color = $data['line_color'];
        if ($data['is_hyaline']) $this->is_hyaline = $data['is_hyaline'];
    }

    //生成小程序二维码
    function getWxacode()
    {
        $token = $this->getToken();

        if ($token === false) {return false;}

        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=".$token;
        $json_data = [
            "scene" => $this->param,
            "page"  => $this->page,
            "width" => $this->width
        ];

        if ($this->auto_color)
        {
            $json_data['auto_color'] = $this->auto_color;
        }

        if (!empty($this->line_color))
        {
            $json_data['line_color'] = $this->line_color;
        }

        if ($this->is_hyaline)
        {
            $json_data['is_hyaline'] = $this->is_hyaline;
        }

        $res = self::curl_post($url, json_encode($json_data), $options = array());//请求生成二维码

        if (isset($res['errcode'])) {
            \Log::debug('===生成小程序二维码获取失败====='. self::class, $res);
            return false;
        }

        return $res;
    }

    //发送获取token请求,获取token(有效期2小时)
    public function getToken()
    {
        $set = \Setting::get('plugin.min_app');

        $paramMap = [
            'grant_type' => 'client_credential',
            'appid' => $set['key'],
            'secret' => $set['secret'],
        ];
        //获取token的url参数拼接
        $strQuery="";
        foreach ($paramMap as $k=>$v){
            $strQuery .= strlen($strQuery) == 0 ? "" : "&";
            $strQuery.=$k."=".urlencode($v);
        }

        $getTokenUrl = "https://api.weixin.qq.com/cgi-bin/token?". $strQuery; //获取token的url

        $client = new Client;
        $res = $client->request('GET', $getTokenUrl);

        $data = json_decode($res->getBody()->getContents(), JSON_FORCE_OBJECT);

        if (isset($data['errcode'])) {
            \Log::debug('===生成小程序二维码获取token失败====='. self::class, $data);
            return false;
        }
        return $data['access_token'];
    }

    public function curl_post($url = '', $postdata = '', $options = array())
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}