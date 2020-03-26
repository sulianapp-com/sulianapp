<?php
/**
 * 商品分享权限关联表数据操作
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/24
 * Time: 下午2:31
 */

namespace app\backend\modules\goods\models;


use app\common\traits\MessageTrait;

class Share extends \app\common\models\goods\Share
{
    static protected $needLog = true;

    use MessageTrait;
    public $timestamps = true;

    /**
     * 获取商品分享关注数据
     * @param int $goodsId
     * @return array
     */
    public static function getInfo($goodsId)
    {
        return self::getGoodsShareInfo($goodsId);
    }

    public static function relationSave($goodsId, $data, $operate)
    {
        if(!$goodsId){
            return false;
        }
        if (!$data) {
            return false;
        }
        $shareModel = self::getModel($goodsId, $operate);
        //判断deleted
        if ($operate == 'deleted') {
            return $shareModel->delete();
        }
        $data['goods_id'] = $goodsId;
        $shareModel->setRawAttributes($data);
        return $shareModel->save();
    }
    public static function relationValidator($goodsId, $data, $operate)
    {
        $flag = false;
        $model = new static;
        $validator = $model->validator($data);
        if($validator->fails()){
            $model->error($validator->messages());
        }else{
            $flag = true;
        }
        return $flag;
    }

    public static function getModel($goodsId,$operate)
    {
        $model = false;
        if($operate != 'created') {
            $model = static::where(['goods_id' => $goodsId])->first();
        }
        !$model && $model =  new static;

        return $model;
    }

    /**
     * 商品分享关注数据添加
     * @param array $shareInfo
     * @return bool
     */
    public static function createdShare($shareInfo)
    {
        return self::insert($shareInfo);
    }

    /**
     * 商品分享关注数据更新
     * @param array $shareInfo
     * @return mixed
     */
    public static function updatedShare($goodsId, $shareInfo)
    {
        return self::where('goods_id', $goodsId)->update($shareInfo);
    }

    /**
     * 商品分享关注数据删除
     * @param int $goodsId
     * @return mixed
     */
    public static function deletedShare($goodsId)
    {
        return self::where('goods_id', $goodsId)->delete();
    }

}