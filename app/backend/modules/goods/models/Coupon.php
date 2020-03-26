<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/13
 * Time: 上午16:45
 */

namespace app\backend\modules\goods\models;


use app\common\models\goods\GoodsCoupon;
use app\common\traits\MessageTrait;

class Coupon extends GoodsCoupon
{
    //use MessageTrait;


    static protected $needLog = true;



    /**
     * @param $goodsId
     * @param $data
     * @param $operate
     * @return bool
     */
    public function relationValidator($goodsId, $data, $operate)
    {
        $couponModel = self::getModel($goodsId,$operate);

        $array = [
            'goods_id'      => $goodsId,
            'is_give'       => $data['is_give']?:0,
            'send_type'     => $data['send_type']?:0,
            'send_num'      => $data['send_num']?:0,
            'coupon'        => $couponModel->recombination($data),
            'shopping_share' => $data['shopping_share']?:'0',
            'share_coupon'  => $couponModel->recombination($data['share_coupon']),
        ];

        $couponModel->fill($array);
        $validator = $couponModel->validator();

        if ($validator->fails()) {
            $this->error($validator->messages());
            return false;
        }
        return true;
    }


    /**
     * @param $goodsId
     * @param $data
     * @param $operate
     * @return bool|null
     */
    public static function relationSave($goodsId, $data, $operate)
    {
        if (!$goodsId) {
            return false;
        }
        if (!$data) {
            return false;
        }
        $couponModel = self::getModel($goodsId, $operate);

        $array = [
            'goods_id'      => $goodsId,
            'is_give'       => $data['is_give'],
            'send_type'     => $data['send_type'],
            'send_num'      => $data['send_num'] ?: '0',
            'coupon'        => $couponModel->recombination($data),
            'shopping_share' => $data['shopping_share']?:'0',
            'share_coupon'  => $couponModel->recombination($data['share_coupon']),
        ];

        //判断deleted
        if ($operate == 'deleted') {
            return $couponModel->delete();
        }

        $couponModel->fill($array);
        return $couponModel->save();
    }


    /**
     * @param $goodsId
     * @param $operate
     * @return bool|static
     */
    public static function getModel($goodsId, $operate)
    {
        $model = false;
        if ($operate != 'created') {
            $model = Coupon::where(['goods_id' => $goodsId])->first();
        }
        !$model && $model = new Coupon;

        return $model;
    }


    /**
     * @param $data
     * @return array|bool
     */
    public function recombination($data)
    {

        $coupon = [];
        $coupon_ids = is_array($data['coupon_id']) ? $data['coupon_id'] : array();
        foreach ($coupon_ids as $key => $coupon_id) {

            if (!preg_match('/^\+?[1-9]\d*$/', trim($data['coupon_several'][$key]))) {
                $this->error('请输入正确的优惠券赠送数量（正整数）');
                return false;
                break;
            }

            $coupon_id = trim($coupon_id);
            if ($coupon_id) {
                $coupon[] = array(
                    'coupon_id' => trim($data['coupon_id'][$key]),
                    'coupon_name' => trim($data['coupon_name'][$key]),
                    'coupon_several' => trim($data['coupon_several'][$key])
                );
            }
        }
        return $coupon;
    }


}
