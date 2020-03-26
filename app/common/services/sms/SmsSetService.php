<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/9/19 上午10:14
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\common\services\sms;


use app\common\facades\Setting;

class SmsSetService
{
    protected $sms_set;


    public function __construct()
    {
        $this->sms_set = $this->getSmsSet();
    }

    /**
     * sms 是否配置、是否可以使用
     * @return bool
     */
    public function isCanUse()
    {
        switch ($this->sms_set['type']) {
            case 1:
                $result = $this->iHuYi();
                break;
            case 2:
                $result = $this->aLiDaYu();
                break;
            case 3:
                $result = $this->aLiYun();
                break;
            default:
                $result = false;
                break;
        }
        if ($result) {
            return true;
        }
        return false;
    }


    /**
     * sms 互亿无线设置
     * @return array|bool
     */
    protected function iHuYi()
    {
        if ($this->sms_set['account'] && $this->sms_set['password']) {
            return [
                'account' => $this->sms_set['account'],
                'password'=> $this->sms_set['password']
            ];
        }
        return false;
    }


    /**
     * sms 阿里大鱼设置
     * @return array|bool
     */
    protected function aLiDaYu()
    {
        if ($this->sms_set['appkey']
            && $this->sms_set['secret']
            && $this->sms_set['signname']
            && $this->sms_set['templateCode']
            && $this->sms_set['product']
            && $this->sms_set['templateCodeForget']
            && $this->sms_set['forget']
        ) {
            return [
                'appkey' => $this->sms_set['appkey'],
                'secret' => $this->sms_set['secret'],
                'signname' => $this->sms_set['signname'],
                'templateCode'=> $this->sms_set['templateCode'],
                'product' => $this->sms_set['product'],
                'templateCodeForget'=> $this->sms_set['templateCode'],
                'forget' => $this->sms_set['product'],
            ];
        }
        return false;
    }


    /**
     * sms 阿里云设置
     * @return array|bool
     */
    protected function aLiYun()
    {
        if ($this->sms_set['aly_appkey']
            && $this->sms_set['aly_secret']
            && $this->sms_set['aly_signname']
            && $this->sms_set['aly_templateCode']
            && $this->sms_set['aly_templateCodeForget']
        ) {
            return [
                'aly_appkey' => $this->sms_set['aly_appkey'],
                'aly_secret' => $this->sms_set['aly_secret'],
                'aly_signname' => $this->sms_set['aly_signname'],
                'aly_templateCode'=> $this->sms_set['aly_templateCode'],
                'aly_templateCodeForget' => $this->sms_set['aly_templateCodeForget'],
            ];
        }
        return false;
    }


    /**
     * sms 全部设置
     * @return mixed
     */
    private function getSmsSet()
    {
        return Setting::get('shop.sms');
    }
}
