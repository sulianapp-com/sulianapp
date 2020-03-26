<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/3
 * Time: 11:54
 */

namespace app\common\services\operation;


class BalanceSetLog extends OperationBase
{
    public $modules = 'finance';

    public $type = 'balance';

    private $default = false;

    protected function modifyDefault()
    {

        if (isset($this->model['type'])) {
            $this->default = true;
            $this->setLog('mark', $this->model['type']);
            $this->setLog('type', 'withdraw_balance');
        } else {
            $this->setLog('mark', 'finance.balance');
        }

    }

    protected function special()
    {

    }

    /**
     * 获取模型需要记录的字段
     * @return mixed
     */
    protected function recordField()
    {
        $balance = [
            'recharge' => ['field_name'=>'账户充值', 0=>'关闭账户充值', 1=>'开启账户充值' ],
            'recharge_activity' => ['field_name'=>'充值活动', 0=>'关闭充值活动', 1=>'开启充值活动', 2=>'重置充值活动' ],
            'proportion_status' => ['field_name'=>'充值满额送', 0=>'赠送固定金额', 1=>'赠送充值比例'],

        ];

        $withdraw_balance = [
            'status' => ['field_name'=>'开启余额提现', 0=>'关闭', 1=>'开启'],
            'wechat' => ['field_name'=>'余额提现到微信', 0=>'关闭', 1=>'开启'],
            'alipay' => ['field_name'=>'余额提现到支付宝', 0=>'关闭', 1=>'开启'],
            'balance_manual' => ['field_name'=>'余额手动提现', 0=>'关闭', 1=>'开启'],
            'poundage_type'  => ['field_name'=>'余额提现手续费',0=>'手续费比例', 1=>'固定金额'],
            'withdrawmoney'  => '余额提现限制(最小金额值)',

        ];


        if ($this->default) {
            return $withdraw_balance;
        }

        return $balance;
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