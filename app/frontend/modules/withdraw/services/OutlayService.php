<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/6/11 下午2:24
 * Email: livsyitian@163.com
 */

namespace app\frontend\modules\withdraw\services;


use app\common\exceptions\AppException;
use app\common\facades\Setting;
use app\frontend\modules\withdraw\models\Withdraw;

class OutlayService
{
    /**
     * @var array
     */
    private $income_set;


    /**
     * @var array
     */
    private $withdraw_set;


    /**
     * @var Withdraw
     */
    private $withdrawModel;


    public function __construct(Withdraw $withdrawModel)
    {
        $this->withdrawModel = $withdrawModel;
        $this->income_set = $this->incomeSet();
        $this->withdraw_set = $this->withdrawSet();
    }


    /**
     * @return float
     */
    public function getPoundageRate()
    {
        return $this->getIncomeSet('poundage_rate');
    }

    public function getPoundageType()
    {
        return $this->withdrawModel->poundage_type;
    }


    /**
     * @return float
     */
    public function getPoundage()
    {
        $rate = $this->getPoundageRate();
        $amount = $this->getWithdrawAmount();
        $type = $this->getPoundageType();

        return $this->calculate($amount, $rate,$type);
    }


    /**
     * @return float
     */
    public function getServiceTaxRate()
    {
        if(in_array($this->withdrawModel->mark, ['StoreCashier','StoreWithdraw','StoreBossWithdraw']))
        {
            return 0;
        }
        return $this->getWithdrawSet('servicetax_rate');
    }


    /**
     * 劳务税 = （提现金额 - 手续费）* 劳务税比例
     *
     * @return float
     */
    public function getServiceTax()
    {
        $rate = $this->getServiceTaxRate();
        $amount = $this->getWithdrawAmount();
        if(!(\Setting::get('withdraw.income.service_tax_calculation'))){
            $withdraw_poundage = $this->getPoundage();
            $amount = bcsub($amount, $withdraw_poundage, 2);
        }
        
        return $this->calculate($amount, $rate);
    }


    /**
     * @return float
     */
    public function getToBalancePoundageRate()
    {
        return $this->getWithdrawSet('special_poundage');
    }

    /**
     * @return float
     */
    public function getToBalancePoundageType()
    {
        return $this->getWithdrawSet('special_poundage_type');
    }


    /**
     * @return float
     */
    public function getToBalancePoundage()
    {
        $rate = $this->getToBalancePoundageRate();
        $amount = $this->getWithdrawAmount();
        $type = $this->getToBalancePoundageType();

        return $this->calculate($amount, $rate,$type);
    }


    /**
     * @return float
     */
    public function getToBalanceServiceTaxRate()
    {
        if(in_array($this->withdrawModel->mark, ['StoreCashier','StoreWithdraw','StoreBossWithdraw']))
        {
            return 0;
        }
        return $this->getWithdrawSet('special_service_tax');
    }


    /**
     * 余额独立
     * 劳务税  = （提现金额 - 手续费）* 劳务税比例
     * @return float
     */
    public function getToBalanceServiceTax()
    {
        $rate = $this->getToBalanceServiceTaxRate();
        $amount = $this->getWithdrawAmount();
        if(!(\Setting::get('withdraw.income.service_tax_calculation'))){
            $withdraw_poundage = $this->getToBalancePoundage();
            $amount = bcsub($amount, $withdraw_poundage, 2);
        }
        return $this->calculate($amount, $rate);
    }


    /**
     * @return float
     */
    private function getWithdrawAmount()
    {
        return $this->withdrawModel->amounts;
    }


    /**
     * Calculate
     *
     * @param $amount
     * @param $rate
     * @return float
     */
    private function calculate($amount, $rate,$type=0)
    {
        if($type == 1)
        {
            return $rate;
        }
        return bcdiv(bcmul($amount, $rate, 2), 100, 2);
    }


    private function getIncomeSet($key)
    {
        $result = array_get($this->income_set, $key, '0.00');

        return empty($result) ? '0.00' : $result;
    }


    /**
     * @param $key
     * @return string
     */
    private function getWithdrawSet($key)
    {
        $result = array_get($this->withdraw_set, $key, '0.00');

        return empty($result) ? '0.00' : $result;
    }


    /**
     * @return array
     */
    private function withdrawSet()
    {
        return $this->withdrawModel->withdraw_set;
        //return Setting::get('withdraw.income');
    }


    private function incomeSet()
    {
        return $this->withdrawModel->income_set;
        /*$mark = $this->withdrawModel->mark;
        if (!$mark) {
            throw new AppException('Mark error!');
        }
        if (!empty($this->withdrawModel->income_set)) {
            return $this->withdrawModel->income_set;
        }
        return Setting::get('withdraw.' . $mark);*/
    }

}
