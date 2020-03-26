<?php

namespace app\common\models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 该表用于记录禁止编辑商品的信息
 * 当要禁止某商品编辑时，就往该表插入一条数据，当可以编辑后，请删除该记录
 * 例如，拼团活动使用了商品id:635,这时候需要禁止商品635编辑功能，否则可能出现下单出错(规格问题)
 *  于是拼团活动往该表插入一条数据，活动结束后将该数据删除
 * 当拼团活动在进行中而编辑该商品时，会查询该表是否有该商品记录，有则不允许编辑
 * 需要记录edit_key字段，如拼团和抢购都设置了商品不可编辑，那么拼团不应该删除抢购写入的数据，所以要通过edit_key区分
 * Class GoodsEditDisable
 * @property int goods_id 商品id
 * @property int message 信息
 * @property int edit_key 关键字，通过该关键字，创建对应的记录和删除对应的记录
 * @package app\common\models
 */
class GoodsEditDisable extends \app\common\models\BaseModel
{
    public $table = 'yz_goods_edit_disable';

    use SoftDeletes;
    public $timestamps = true;
    public $attributes = [];

    // 拼团活动创建时禁止商品编辑使用的关键字
    const FIGHT_GROUPS_CREATE_KEY = 'plugin_fight_groups_create';

    public function rules()
    {
        return [
            'uniacid' => 'required|integer',
            'goods_id' => 'required|integer',
            'message' => 'required',
            'edit_key' => 'required'
        ];
    }

    public function atributeNames()
    {
        return [
            'uniacid' => '公众号ID',
            'goods_id' => '商品id',
            'message' => '提示信息',
            'edit_key' => '关键字'
        ];
    }

}