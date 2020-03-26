<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/8
 * Time: 9:37
 */

namespace app\backend\modules\goods\models;


use app\common\traits\MessageTrait;

class InvitePage extends \app\common\models\goods\InvitePage
{
    use MessageTrait;

    public static function relationSave($goods_id, $data, $operate)
    {
        if (!$goods_id) {
            return false;
        }

        if ($operate == 'deleted') {
            //\app\common\models\goods\InvitePage::getDataByGoodsId($goods_id)->delete();
            $goods_delete = \app\common\models\goods\InvitePage::getDataByGoodsId($goods_id);
            if ($goods_delete){
                $goods_delete->delete();
            }
        }

        $inviteModel = InvitePage::getModel($goods_id, $operate);

        $inviteModel['goods_id'] = $goods_id;
        $inviteModel['uniacid'] = \YunShop::app()->uniacid;
        $inviteModel['status'] = $data['status']?:0;

        return $inviteModel->save();
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