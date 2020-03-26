<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/10/22
 * Time: 上午11:58
 */

namespace app\common\models\point;


use app\common\models\BaseModel;
use app\common\observers\point\RechargeObserver;
use app\common\traits\CreateOrderSnTrait;

/**
 * Class RechargeModel
 * @package app\common\models\point
 */
class RechargeModel extends BaseModel
{
    use CreateOrderSnTrait;

    protected $table = 'yz_point_recharge';

    protected $guarded = [''];

    /**
     * todo 应该 存在一个状态服务的常量集
     *
     * Recharge state error.
     */
    const STATUS_ERROR = -1;

    /**
     * todo 应该 存在一个状态服务的常量集
     *
     * Recharge state success.
     */
    const STATUS_SUCCESS = 1;


    public function member()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'member_id');
    }


    public static function boot()
    {
        parent::boot();
        self::observe(new RechargeObserver());
    }

    /**
     * 定义字段名
     *
     * @return array
     */
    public  function atributeNames() {
        return [
            'uniacid'   => "公众号ID",
            'member_id' => "会员ID",
            'money'     => '充值金额',
            'type'      => '充值类型',
            'order_sn'  => '充值订单号',
            'status'    => '状态',
            'remark'    => '备注信息'
        ];
    }
}
