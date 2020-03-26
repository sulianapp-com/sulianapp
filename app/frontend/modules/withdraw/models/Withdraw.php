<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/6/11 上午9:26
 * Email: livsyitian@163.com
 */

namespace app\frontend\modules\withdraw\models;

class Withdraw extends \app\common\models\Withdraw
{
    /**
     * 提现的收入唯一标识
     *
     * @var string
     */
    public $mark;


    /**
     * 提现设置
     *
     * @var array
     */
    public $withdraw_set;

    //todo 自动提现临时使用需要优化
    public $is_auto;


    /**
     * 提现的收入对应设置
     *
     * @var array
     */
    public $income_set;


    /**
     * @return array
     */
    public function atributeNames()
    {
        return [
            'withdraw_sn'       => "提现单号",
            'uniacid'           => "公众号ID",
            'member_id'         => "会员ID",
            'type'              => "提现类型",
            'type_name'         => "",
            'type_id'           => "",
            'amounts'           => "提现金额",
            'poundage'          => "手续费",
            'poundage_rate'     => "手续费比例",
            'poundage_type'     => "手续费类型",
            'actual_poundage'   => "实际手续费",
            'actual_amounts'    => "实际提现金额",
            'servicetax'        => "劳务税",
            'servicetax_rate'   => "劳务税比例",
            'actual_servicetax' => "实际劳务税",
            'pay_way'           => "打款方式",
            'manual_type'       => "手动打款方式",
            'status'            => "提现状态"
        ];
    }


    /**
     * @return array
     */
    public function rules()
    {
        return  [
            'withdraw_sn'       => "required",
            'uniacid'           => "required",
            'member_id'         => "required",
            'type'              => "required",
            'type_name'         => "",
            'type_id'           => "",
            'amounts'           => "required",
            'poundage'          => "numeric|min:0",
            'poundage_rate'     => "numeric|min:0",
            'poundage_type'     => "",
            'actual_poundage'   => "numeric|min:0",
            'actual_amounts'    => "numeric|min:0",
            'servicetax'        => "numeric|min:0",
            'servicetax_rate'   => "numeric|min:0",
            'actual_servicetax' => "numeric|min:0",
            'pay_way'           => "",
            'manual_type'       => "required",
            'status'            => ""
        ];
    }

}
