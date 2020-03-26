<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/11
 * Time: 上午10:39
 */

namespace app\backend\modules\finance\services;

use app\common\traits\MessageTrait;
use Setting;

class PointService
{
    use MessageTrait;


    /**
     * 验证设置数组
     *
     * @param array $point_data
     * @return bool|string
     * @author yangyang
     */
    public function verifyPointData($point_data)
    {
        if ($point_data['money_max'] > 100) {
            $this->error('商品最高抵扣积分不能超过100%');
        } elseif ($point_data['transfer_love_rate'] > 100) {
            $this->error('自动转入比例不能大于100');
        } elseif ($point_data['point_transfer_poundage'] > 100) {
            $this->error('手续费比例不能大于100');
        } else {
            Setting::set('point.set', $point_data);
            return '积分基础设置保存成功';
        }

        return false;
    }

    /**
     * 获取积分基础设置
     *
     * @param array $point_data
     * @param array $enoughs_data
     * @param array $give
     * @return array
     * @author yangyang
     */
    public static function getPointData($point_data, $enoughs_data, $give)
    {
        if (!empty($enoughs_data)) {
            $enoughs = [];
            foreach ($enoughs_data as $key => $value) {
                $enough = floatval($value);
                if ($enough > 0) {
                    $enoughs[] = array('enough' => floatval($enoughs_data[$key]), 'give' => floatval($give[$key]));
                }
            }
            $point_data['enoughs'] = $enoughs;
        }
        return $point_data;
    }
}