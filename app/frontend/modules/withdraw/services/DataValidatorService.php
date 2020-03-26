<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/6/11 下午3:30
 * Email: livsyitian@163.com
 */

namespace app\frontend\modules\withdraw\services;


use app\common\exceptions\AppException;
use app\frontend\modules\withdraw\models\Income;
use app\frontend\modules\withdraw\models\Withdraw;

class DataValidatorService
{
    const  WITHDRAW_TYPE_WECHAT   = 'wechat';

    const  WITHDRAW_TYPE_ALIPAY   = 'alipay';
    /**
     * @var Withdraw
     */
    private $withdrawModel;


    /**
     * @var array
     */
    private $income_set;


    public function __construct(Withdraw $withdrawModel)
    {
        $this->withdrawModel = $withdrawModel;
        $this->income_set = $this->incomeSet();
    }


    /**
     * @throws AppException
     */
    public function validator()
    {
        if($this->withdrawModel->is_auto) {
            return true;
        }
        $type_name = $this->withdrawModel->type_name;

        // 12月20号修改 原：提现金额不能小于1
        $amount = $this->withdrawModel->amounts;
        $type   = $this->withdrawModel->pay_way;

        //微信支付宝提现限制
        $this->cashLimitation($type,$type_name);

        if (bccomp($amount, 0, 2) == -1) {
            throw new AppException("{$type_name}提现金额不能小于0元");
        }

        $real_amount = $this->getIncomeAmount();
        if (bccomp($amount, $real_amount, 2) != 0) {
            throw new AppException("{$type_name}提现金额错误！");
        }

        $roll_out_limit = $this->getRollOutLimit();
        if (bccomp($amount, $roll_out_limit, 2) == -1 && $type != self::WITHDRAW_TYPE_WECHAT && $type != self::WITHDRAW_TYPE_ALIPAY ) {
            throw new AppException("{$type_name}提现金额不能小于{$roll_out_limit}元");
        }

        // 12月20号修改 原：扣除手续费、劳务税金额不能小于1元
        $outlay = bcadd($this->withdrawModel->poundage, $this->withdrawModel->servicetax, 2);
        $result_amount = bcsub($amount, $outlay, 2);
        if (bccomp($result_amount, 0, 2) == -1) {
            throw new AppException("{$type_name}扣除手续费、劳务税金额不能小于0元");
        }
    }


    /**
     * @return float
     */
    private function getIncomeAmount()
    {
        $type_ids = $this->withdrawModel->type_id;

        //->whereStatus(Income::STATUS_INITIAL)
        return Income::whereIn('id', explode(',', $type_ids))->whereStatus(Income::STATUS_INITIAL)->sum('amount');
    }


    /**
     * @return float
     */
    private function getRollOutLimit()
    {
        return $this->getIncomeSet('roll_out_limit');
    }


    /**
     * @param $key
     * @return float
     */
    private function getIncomeSet($key)
    {
        $result = array_get($this->income_set, $key, '0');
        return empty($result) ? '0' : $result;
    }


    /**
     * @return array
     */
    private function incomeSet()
    {
        return $this->withdrawModel->income_set;
    }

    private function withdrawIncomeSet()
    {
        return \Setting::get('withdraw.income');
    }




    private function cashLimitation($type,$type_name){

        $set = $this->withdrawIncomeSet();

        if( $type == self::WITHDRAW_TYPE_WECHAT){
            $wechat_min =  $set['wechat_min'] ;
            $wechat_max =  $set['wechat_max'] ;
            if( $this->withdrawModel->amounts < $wechat_min && !empty($wechat_min)){
                throw new AppException("{$type_name}提现到微信单笔提现额度最低{$wechat_min}元",['status'=>0]);
            }elseif( $this->withdrawModel->amounts > $wechat_max && !empty($wechat_max)){
                throw new AppException("{$type_name}提现到微信单笔提现额度最高{$wechat_max}元",['status'=>0]);
            }
        }elseif($type == self::WITHDRAW_TYPE_ALIPAY){
            $alipay_min =  $set['alipay_min'] ;
            $alipay_max =  $set['alipay_max'] ;
            if( $this->withdrawModel->amounts < $alipay_min && !empty($alipay_min)){
                throw new AppException("{$type_name}提现到支付宝单笔提现额度最低{$alipay_min}元",['status'=>0]);
            }elseif( $this->withdrawModel->amounts > $alipay_max && !empty($alipay_max)){
                throw new AppException("{$type_name}提现到支付宝单笔提现额度最高{$alipay_max}元",['status'=>0]);
            }
        }
    }

}
