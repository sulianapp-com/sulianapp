<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/3
 * Time: 13:55
 */

namespace app\common\services\operation;


class PointSetLog extends OperationBase
{
    public $modules = 'finance';

    public $type = 'point';

    protected function modifyDefault()
    {
        if (isset($this->model['type'])) {
            $this->setLog('mark', $this->model['type']);
        } else {
            $this->setLog('mark', 'point.set');
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
        return [
            'point_transfer' => ['field_name'=>'积分转让', 0=>'关闭', 1=>'开启' ],
            'point_deduct' => ['field_name'=>'积分抵扣', 0=>'关闭', 1=>'开启' ],
            'point_rollback' => ['field_name'=>'积分抵扣', 0=>'关闭', 1=>'开启' ],
            'point_freight' => ['field_name'=>'积分抵扣运费', 0=>'关闭', 1=>'开启' ],
            'money'  => '积分抵扣比例(元)',
            'money_max'  => '积分商品最高抵扣%',
            'give_point' => '购买商品赠送积分',
            'enough_money_and_enough_point' => '消费赠送',

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
        array_push($keys, 'enough_money', 'enough_point');

        $old = array_only($model['old'], $keys);

        $new = array_only($model['new'], $keys);

        if (empty($old) || empty($new)) {
            return [];
        }

        if (($old['enough_money'] != $new['enough_money']) || ($old['enough_point'] != $new['enough_point']) ) {
            $this->modify_fields['enough_money_and_enough_point']['old_content'] = '单笔订单满'.$old['enough_money'].'元 赠送'.$old['enough_point'].'积分';
            $this->modify_fields['enough_money_and_enough_point']['new_content'] = '单笔订单满'.$new['enough_money'].'元 赠送'.$new['enough_point'].'积分';
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