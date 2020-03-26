<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/9/16 下午4:58
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\common\services\password;


use app\common\exceptions\PaymentException;
use app\common\models\MemberShopInfo;

class PasswordService
{
    private $auth_key;


    public function __construct()
    {
        $this->auth_key = \YunShop::app()->config['setting']['authkey'];
    }


    public function checkMemberPassword($memberId, $password)
    {
        $memberModel = MemberShopInfo::select('pay_password', 'salt')->where('member_id', $memberId)->first();
        if(!\Setting::get('shop.pay.balance_pay_proving')){
            // 商城关闭支付密码
            throw (new PaymentException())->settingClose();
        }
        if (!isset($memberModel->pay_password) || empty($memberModel->pay_password)) {
            // 用户未设置
            throw (new PaymentException())->notSet();
        }

        if (!$this->check($password, $memberModel->pay_password, $memberModel->salt)) {
            // 密码不匹配
            throw (new PaymentException())->passwordError();
        }
        return true;
    }

    /**
     * 生成哈希加密密码值
     * @param $password
     * @param $salt
     * @return string
     */
    public function make($password, $salt)
    {
        $password = "{$password}-{$salt}-{$this->auth_key}";
        return sha1($password);
    }


    /**
     * 创建密码
     * @param $password
     * @return array
     */
    public function create($password)
    {
        $salt = $this->randNum(8);
        return ['password' => $this->make($password, $salt), 'salt' => $salt];
    }


    /**
     * 密码验证
     * @param $password
     * @param $sha1_value
     * @param $salt
     * @return bool
     */
    public function check($password, $sha1_value, $salt)
    {
        return $sha1_value == $this->make($password, $salt) ? true : false;
    }


    /**
     * 获取随机字符串
     * @param number $length 字符串长度
     * @param boolean $numeric 是否为纯数字
     * @return string
     */
    public function randNum($length, $numeric = FALSE)
    {
        $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
        if ($numeric) {
            $hash = '';
        } else {
            $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
            $length--;
        }
        $max = strlen($seed) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $seed{mt_rand(0, $max)};
        }
        return $hash;
    }

}
