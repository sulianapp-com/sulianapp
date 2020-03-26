<?php
namespace app\frontend\modules\finance\models;


use app\common\models\Income;
use Illuminate\Support\Facades\Config;

class Withdraw extends \app\common\models\Withdraw
{
    public $Incomes;

    protected $appends = ['incomes'];

    public static function getWithdrawLog($status)
    {
        $withdrawModel = self::select('id', 'type_name', 'amounts', 'poundage', 'status', 'created_at');

        $withdrawModel->uniacid();

        $withdrawModel->where('member_id', \YunShop::app()->getMemberId());
        if ($status != '') {
            $withdrawModel->where('status', $status);
        }
        return $withdrawModel;
    }

    public static function getWithdrawInfoById($id)
    {
        $withdrawModel = self::select('id', 'withdraw_sn', 'pay_way', 'type', 'type_id', 'type_name', 'amounts', 'poundage', 'status', 'created_at', 'actual_amounts', 'actual_poundage');
        $withdrawModel->uniacid();
        $withdrawModel->where('id', $id);


        return $withdrawModel;
    }

    public function getIncomesAttribute()
    {

        if (!isset($this->Incomes)) {
            $configs = \app\backend\modules\income\Income::current()->getItems();
            foreach ($configs as $key => $config) {
                if ($config['class'] === $this->type) {
                    $incomes = Income::getIncomeByIds($this->type_id)
                        ->select('id', 'incometable_type','incometable_id')
                        ->get();
                    foreach ($incomes as $key => $income) {
                        $this->Incomes[$key] = $income->incometable->toArray();
                    }
                }
            }
        }
        return $this->Incomes;
    }

    /**
     * 验证提现订单号唯一性
     *
     * @param $withdrawSN
     * @return mixed
     * @Author yitian */
    public static function validatorOrderSn($withdrawSN)
    {
        return self::uniacid()->where('withdraw_sn', $withdrawSN)->first();
    }

    /**
     * 定义字段名
     * @return array
     * @Author yitian */
    public  function atributeNames() {
        return [
            'withdraw_sn'       => "提现订单号",
            'uniacid'           => "公众号ID",
            'member_id'         => '会员ID',
            'type'              => '提现类型',
            //'type_id'           => '充值订单号不能为空',
            'type_name'         => '状态',
            'amounts'           => "提现金额",
            //'poundage'          => "会员ID不能为空",
            //'poundage_rate'     => "会员ID不能为空",
            //'pay_way'           => "提现类型",
            //'status'            => "会员ID不能为空",
            //'audit_at'          => "会员ID不能为空",
            //'pay_at'            => "会员ID不能为空",
            //'arrival_at'        => "会员ID不能为空",
            //'actual_amounts'    => "会员ID不能为空",
            //'actual_poundage'   => "会员ID不能为空"
        ];
    }

    /**
     * 字段规则
     * @return array
     * @Author yitian */
    public  function rules()
    {
        return [
            'withdraw_sn'       => "required",
            'uniacid'           => "required|numeric",
            'member_id'         => 'required|numeric',
            'type'              => 'required',
            //'type_id'           => '',
            'type_name'         => 'required',
            'amounts'           => "numeric|regex:/^(?!0+(?:\.0+)?$)\d+(?:\.\d{1,2})?$/",
            //'poundage'          => "",
            //'poundage_rate'     => "",
            //'pay_way'           => "",
            //'status'            => "",
            //'audit_at'          => "",
            //'pay_at'            => "",
            //'arrival_at'        => "",
            //'actual_amounts'    => "",
            //'actual_poundage'   => ""
        ];
    }

}