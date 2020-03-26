<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/2
 * Time: 17:55
 */

namespace app\common\services\operation;

use app\backend\modules\member\models\Member;

class MemberBankCardLog extends OperationBase
{
    public $modules = 'member';

    public $type = 'dank_card';

    protected function modifyDefault()
    {
        if (is_array($this->model)) {
            $this->setLog('mark', $this->model['uid']);
        } else {
            $this->setLog('mark', $this->model->getOriginal('member_id'));
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
            'member_name'    => '银行卡真实姓名',
            'bank_name'      => '银行卡开户行',
            'bank_province'  => '银行卡开户行省份',
            'bank_city'      => '银行卡开户城市',
            'bank_branch'    => '银行卡开户支行',
            'bank_card'      => '银行卡号',

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

        if (is_null($model->getOriginal())) {
            return [];
        }

        $nick_name = Member::getMemberByUid($model->member_id)->value('nickname');

        foreach ($this->recordField() as $key => $item) {
            if ($model->isDirty($key)) {

                $this->modify_fields[$key]['old_content'] = $nick_name.':'.$model->getOriginal($key);
                $this->modify_fields[$key]['new_content'] = $nick_name.':'.$model->{$key};

            }
        }

        return $this->modify_fields;
    }
}