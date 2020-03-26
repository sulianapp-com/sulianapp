<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/23 下午2:21
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\common\models\member;


use app\common\models\BaseModel;

class BankCard extends BaseModel
{
    protected $table = 'yz_member_bank_card';

    protected $guarded = [''];


    public function member()
    {
        return $this->belongsTo('app\common\models\Member', 'member_id', 'uid');
    }

    /**
     * 定义字段名
     *
     * @return array
     */
    public function atributeNames()
    {
        return [
            'member_name' => '会员姓名',
            'bank_card' => '银行卡号 ',
            'bank_name' => '开户行 ',
            'bank_province' => '开户行省份',
            'bank_city' => '开户城市',
            'bank_branch' => '开户支行'
        ];
    }

    /**
     * 字段规则
     *
     * @return array
     */
    public function rules()
    {
        return [
            'member_name' => 'required|max:45',
            'bank_card' => 'required|max:100',
            'bank_name' => 'required|max:45',
            'bank_province' => 'required|max:45',
            'bank_city' => 'required|max:45',
            'bank_branch' => 'required|max:45'
        ];
    }

}
