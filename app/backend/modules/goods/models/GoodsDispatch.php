<?php
/**
 * 配送模板数据操作
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/24
 * Time: 下午2:31
 */

namespace app\backend\modules\goods\models;


use app\common\traits\MessageTrait;

class GoodsDispatch extends \app\common\models\goods\GoodsDispatch
{
    static protected $needLog = true;

    use MessageTrait;
    //public $timestamps = false;
    public $attributes = [
        'dispatch_id' => 0,
        'dispatch_price' => 0,
        'dispatch_type' => 1,
        'is_cod' => 1,
    ];
    /**
     * 获取商品配送信息关联数据
     * @param int $goodsId
     * @return array
     */
    public static function getInfo($goodsId)
    {
        return self::getDispatchInfo($goodsId);
    }

    public static function relationSave($goodsId, $data, $operate)
    {
        if(!$goodsId){
            return false;
        }
        if (!$data) {
            return false;
        }
        $dispatchModel = self::getModel($goodsId, $operate);
        //判断deleted
        if ($operate == 'deleted') {
            return $dispatchModel->delete();
        }
        $data['goods_id'] = $goodsId;
        $dispatchModel->setRawAttributes($data);
        return $dispatchModel->save();
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
     * 商品配送信息关联数据添加
     * @param array $DispatchInfo
     * @return bool
     */
    public static function createdDispatch($DispatchInfo)
    {
        return self::insert($DispatchInfo);
    }

    /**
     * 商品配送信息关联数据更新
     * @param array $DispatchInfo
     * @return mixed
     */
    public static function updatedDispatch($dispatchId, $DispatchInfo)
    {
        return self::where('id', $dispatchId)->update($DispatchInfo);
    }

    /**
     * 商品配送信息关联数据删除
     * @param int $goodsId
     * @return mixed
     */
    public static function deletedDispatch($dispatchId)
    {
        return self::where('id', $dispatchId)->delete();
    }


    public static function deletedGoodsID($dispatchId)
    {
        return self::where('goods_id', $dispatchId)->delete();
    }

    public static function freightSave($goodsId, $data, $operate = '')
    {
        if (!$goodsId) {
            return false;
        }
        if (!$data) {
            return false;
        }


        self::deletedGoodsID($goodsId);
        $datas=[
            'goods_id'=>$goodsId,
            'dispatch_type'=>$data['freight_type'],
            'is_cod'=>$data['is_cod'],
        ];

        if ($data['freight_type']==1) {
            $datas['dispatch_price']=$data['freight_value'];
        }else{
            $datas['dispatch_id']=$data['template_id'];
        }

        return self::relationSave($goodsId,$datas,"created");


    }

}