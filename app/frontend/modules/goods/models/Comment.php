<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/3
 * Time: 下午2:35
 */
namespace app\frontend\modules\goods\models;


class Comment extends \app\common\models\Comment
{

    public $Append;

    protected $appends = ['append','type_name'];

    public static function getCommentsByGoods($goods_id)
    {
        return self::select(
            'id', 'order_id', 'goods_id', 'uid', 'nick_name', 'head_img_url', 'content', 'level',
            'images', 'created_at', 'type')
            ->uniacid()
            ->with(['hasManyReply'=>function ($query) {
                return $query->where('type', 2)
                    ->orderBy('created_at', 'asc');
            }])
            ->where('goods_id', $goods_id)
            ->where('comment_id', 0)
            ->orderBy('created_at', 'acs');
    }


    public function getAppendAttribute()
    {
        if (!isset($this->Append)) {
            $this->Append = static::getAppendById($this->id);
        }
        return $this->Append;
    }

    public static function getAppendById($id)
    {

        return self::uniacid()
            ->where('comment_id', $id)
            ->where('type', 3)
            ->orderBy('created_at', 'asc')
            ->first();
    }

    public static function getOrderGoodsComment()
    {
        return self::uniacid()
            ->with(['hasManyReply'=>function ($query) {
                return $query->where('type', 2)
                    ->orderBy('created_at', 'asc');
            }]);
    }


    public function hasOneOrderGoods()
    {
        return $this->hasOne('app\common\models\OrderGoods', 'order_id', 'order_id');
    }
}