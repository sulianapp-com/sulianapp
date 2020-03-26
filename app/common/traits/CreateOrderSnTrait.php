<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/19 下午4:41
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\common\traits;


trait CreateOrderSnTrait
{
    /**
     * 生成唯一单号
     *
     * @param $prefix //前缀一般为两个大写字母
     * @param string $field //字段不为 order_sn 时需要参数field
     * @param int $length //日期后随机数长度
     * @param bool $numeric //受否为纯数字
     * @return string
     */
    public static function createOrderSn($prefix, $field = 'order_sn', $length = 6, $numeric = true)
    {
        $orderSn = createNo($prefix, $length, $numeric);
        while (1) {
            if (!self::where($field, $orderSn)->first()) {
                break;
            }
            $orderSn = createNo($prefix, $length, $numeric);
        }
        return $orderSn;
    }

}