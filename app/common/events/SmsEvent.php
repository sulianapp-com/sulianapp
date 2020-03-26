<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/12/6
 * Time: 9:50
 */

namespace app\common\events;

class SmsEvent extends \app\common\events\Event
{


    /**
     * @var 手机号码
     */
    public $mobile;
    /**
     * @var 随机验证码
     */
    public $code;
    /**
     * SmsMessage constructor.
     * @param array $params
     *
     */
    private $sms = [];

    function __construct($mobile,$code,$sms)
    {
        $this->mobile = $mobile;
        $this->sms = $sms;
        $this->code   = $code;
    }

    public function getSmsData()
    {
        return $this->sms;
    }
    public function getSmsMobile()
    {
        return $this->mobile;
    }
    public function getSmsCode()
    {
        return $this->code;
    }



}