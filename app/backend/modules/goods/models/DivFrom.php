<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/8/24 下午2:03
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\goods\models;


use app\common\models\GoodsDivFrom;
use app\common\traits\MessageTrait;

class DivFrom extends GoodsDivFrom
{
    use MessageTrait;

    public static function relationSave($goodsId, $data, $operate)
    {
        if (!$goodsId) {
            return false;
        }
        if (!$data) {
            return false;
        }
        $saleModel = self::getModel($goodsId, $operate);
        //判断deleted
        if ($operate == 'deleted') {
            return $saleModel->delete();
        }
        $data['goods_id'] = $goodsId;
        $data['uniacid'] = \YunShop::app()->uniacid;
        $data['status'] = empty($data['status']) ? 0 : $data['status'];
        $saleModel->setRawAttributes($data);

        return $saleModel->save();
    }

    public static function getModel($goodsId, $operate)
    {
        $model = false;
        if ($operate != 'created') {
            $model = static::where(['goods_id' => $goodsId])->first();
        }
        !$model && $model = new static;

        return $model;
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
}