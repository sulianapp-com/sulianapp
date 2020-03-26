<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/8/24 下午2:50
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\common\models;


use app\common\scopes\UniacidScope;

class GoodsDivFrom extends BaseModel
{
    protected $table = 'yz_goods_div_from';

    protected $guarded = [''];

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(new UniacidScope);
    }

    public function scopeOfGoodsId($query,$goodsId)
    {
        return $query->where('goods_id',$goodsId);
    }


    /**
     * 字段验证规则
     * @return array
     */
    public function rules()
    {
        return [
            //'uniacid'         => 'numeric|integer',
            //'goods_id'        => 'numeric|integer',
            'status'            => 'regex:/^[01]$/',
            'explain_title'     => 'max:45/',
            'explain_content'   => '',
        ];
    }


    /**
     * 字段名称
     * @return array
     */
    public function atributeNames()
    {
        return [
            //'uniacid'           => '',
            //'goods_id'        => 'numeric|integer',
            'status'            => '表单状态',
            'explain_title'     => '表单规则标题',
            'explain_content'   => '表单规则内容',
        ];
    }
}
