<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/3
 * Time: 15:17
 */

namespace app\common\services\operation;


class IncomeSetLog extends OperationBase
{
    public $modules = 'finance';

    public $type = 'income';


    protected function modifyDefault()
    {
        if (is_array($this->model)) {
            $this->setLog('mark', 'finance.income');
        } else {
            $this->setLog('mark', $this->model->getOriginal('id'));
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
        $income = [
            'balance' => ['field_name'=>'提现到余额', 0=>'关闭', 1=>'开启'],
            'wechat' => ['field_name'=>'收入提现到微信', 0=>'关闭', 1=>'开启'],
            'alipay' => ['field_name'=>'收入提现到支付宝', 0=>'关闭', 1=>'开启'],
            'manual' => ['field_name'=>'收入手动提现', 0=>'关闭', 1=>'开启'],
            'free_audit'  => ['field_name'=>'收入提现免审核',0=>'关闭', 1=>'开启'],
            'servicetax_rate'  => '劳务税比例(%)',

        ];

        return $income;
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