<?php

namespace app\common\models\goods;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\lValidator;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/22
 * Time: 下午5:54
 */
class Share extends BaseModel
{
    public $table = 'yz_goods_share';
    //public $timestamps = true;

    public $attributes = [
        'need_follow' =>  1,
    ];



    /**
     *  不可填充字段.
     *
     * @var array
     */
    protected $guarded = ['updated_at', 'created_at'];


    /**
     * 自定义显示错误信息
     * @return array
     */
    public static function getGoodsShareInfo($goodsId)
    {
        $goodsShareInfo = self::where('goods_id', $goodsId)
            ->first();
        return $goodsShareInfo;
    }


    /**
     * 自定义字段名
     * 可使用
     * @return array
     */
    public  function atributeNames()
    {
        return [
//            'need_follow' => '强制关注',
            'no_follow_message' => '未关注提示信息',
            'follow_message' => '关注引导信息',
            'share_title' => '分享标题',
            'share_thumb' => '分享图片',
            'share_desc' => '分享描述',
        ];
    }


    public  function rules()
    {
        return [
//            'need_follow' => 'required|digits_between:0,1',
            'no_follow_message' => 'max:255',
            'follow_message' => 'max:255',
            'share_title' => 'max:50',
            'share_thumb' => '',
            'share_desc' => '',
        ];
    }


}