<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/1
 * Time: 18:58
 */

namespace app\common\services\operation;


class ShopPayLog extends OperationBase
{
    public $modules = 'shop';

    public $type = 'pay';

    protected function modifyDefault()
    {
        $this->setLog('mark', 'shop.pay');
    }


    /**
     * 获取模型需要记录的字段
     * @return mixed
     */
    protected function recordField()
    {
        return [
            'weixin'   => ['field_name' => '微信支付',0=> '关闭', 1=>'开启'],
            'weixin_pay' =>  ['field_name' => '标准微信支付',0=> '关闭', 1=>'开启'],
            'weixin_appid' => '微信支付身份标识(appId)',
            'weixin_secret'=> '微信身份密钥(appSecret)',
            'weixin_mchid' => '微信支付商户号(mchId)',
            'weixin_apisecret' => '微信支付密钥(apiSecret)',
            'alipay' => ['field_name' => '支付宝支付',0=> '关闭', 1=>'开启'],
            'alipay_withdrawals' => ['field_name' => '支付宝提现',0=> '关闭', 1=>'开启'],
            'credit' => ['field_name' => '余额支付',0=> '关闭', 1=>'开启'],
            'remittance' => ['field_name' => '银行转账',0=> '关闭', 1=>'开启'],
            'remittance_bank' => '银行转账开户行',
            'remittance_sub_bank' => '银行转账开户支行',
            'remittance_bank_account_name' => '银行转账开户名',
            'remittance_bank_account'  => '银行转账开户账号',

        ];
    }

    /**
     * 获取模型修改了哪些字段
     * @param object array
     * @return array
     */
    protected function modifyField()
    {
        $model = $this->model;

        $keys = array_keys($this->recordField());

        $old = array_only($model['old'], $keys);

        $new = array_only($model['new'], $keys);

        if (empty($old) || empty($new)) {
            return [];
        }

        foreach ($this->recordField() as $key => $item) {
            if ($old[$key] != $new[$key]) {
                $this->modify_fields[$key]['old_content'] = $old[$key];
                $this->modify_fields[$key]['new_content'] = $new[$key];
            }
        }
        return $this->modify_fields;
    }
}