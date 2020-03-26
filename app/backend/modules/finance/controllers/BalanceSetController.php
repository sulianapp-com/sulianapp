<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/12/4 下午2:11
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\finance\controllers;


use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\facades\Setting;
use app\common\helpers\Url;
use app\framework\Support\Facades\Log;

class BalanceSetController extends BaseController
{
    private $balance_set;


    /**
     * 查看余额设置
     * @return string
     */
    public function see()
    {
        !$this->balance_set && $this->setBalanceSet();
        return view('finance.balance.index', [
            'balance' => $this->balance_set,
            'day_data' => $this->getDayData(),
            'memberLevels' => \app\backend\modules\member\models\MemberLevel::getMemberLevelList(),
            'group_type' => \app\common\models\MemberGroup::uniacid()->select('id','group_name')->get()
        ])->render();
    }

    /**
     * 返回一天24时，对应key +1, 例：1 => 0:00
     * @return array
     */
    private function getDayData()
    {
        $dayData = [];
        for ($i = 1; $i <= 23; $i++) {
            $dayData += [
                $i => "每天" . $i . ":00",
            ];
        }
        return $dayData;
    }

    /**
     * 更新余额设置数据
     * @return mixed|string
     */
    public function store()
    {
        $request_data = $this->getPostValue();
        if (Setting::set('finance.balance', $request_data)) {
            (new \app\common\services\operation\BalanceSetLog(['old'=>$this->balance_set,'new'=>$request_data], 'update'));
            return $this->message('余额基础设置保存成功', Url::absoluteWeb('finance.balance-set.see'),'success');
        }

        return $this->see();
    }



    private function getPostValue()
    {
        $this->validate($this->rules(),request(),[],$this->customAttributes());

        $request_data = \YunShop::request()->balance;

        $request_data['sale'] = $this->rechargeSale($request_data);
        $request_data['recharge_activity_start'] = (int)strtotime($request_data['recharge_activity_time']['start']);
        $request_data['recharge_activity_end'] = (int)strtotime($request_data['recharge_activity_time']['end']);

        //顺序不能打乱，需要判断是否重置重置活动
        $request_data['recharge_activity_count'] = $this->getRechargeActivityCount($request_data['recharge_activity']);
        $request_data['recharge_activity'] = ($request_data['recharge_activity'] >= 1) ? 1 : 0;

        unset($request_data['recharge_activity_time']);
        unset($request_data['enough']);
        unset($request_data['give']);

        return $request_data;
    }


    /**
     * 余额基础设置，附值 $this->balance_set
     */
    private function setBalanceSet()
    {
        $this->balance_set = Setting::get('finance.balance');
        if ($this->balance_set['uid']){
            $this->balance_set['member'] = \app\backend\modules\member\models\Member::select('uid','mobile','nickname','realname','avatar')->find($this->balance_set['uid'])->toArray();
        }
    }


    private function getRechargeActivityCount($recharge_activity_status)
    {
        $this->setBalanceSet();

        $activity_count = !empty($this->balance_set['recharge_activity_count']) ? $this->balance_set['recharge_activity_count'] : 1;

        if ($recharge_activity_status == 2) {
            $activity_count += 1;
        }
        return $activity_count;
    }


    /**
     * 处理充值赠送数据，满额赠送数据
     *
     * @param $data
     * @return array
     * @Author yitian */
    private function rechargeSale($data)
    {
        $sale = array();
        $array = is_array($data['enough']) ? $data['enough'] : array();
        foreach ($array as $key => $value) {
            $enough = trim($value);
            if ($enough) {
                $sale[] = array(
                    'enough' => trim($data['enough'][$key]),
                    'give' => trim($data['give'][$key])
                );
            }
        }

        foreach ($sale as $key => $item) {
            $this->validatorCustomRules($item, $this->saleRules(), [], $this->saleCustomAttributes());
        }
        return $sale;
    }


    private function validatorCustomRules($array,$rules,$messages,$customAttributes)
    {
        $validator = $this->getValidationFactory()->make($array, $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            throw new ShopException($validator->errors()->first());
        }
    }


    private function saleRules()
    {
        return [
            'enough'    => 'numeric|min:0',
            'give'      => 'numeric|min:0',
        ];
    }


    private function saleCustomAttributes()
    {
        return [
            'enough'    => "满足金额值",
            'give'      => "赠送金额",
        ];
    }



    private function rules()
    {
        return [
            'balance.recharge'                  => 'required|numeric|regex:/^[01]$/',
            'balance.recharge_activity'         => 'required|numeric|regex:/^[012]$/',
            'balance.recharge_activity_fetter'  => 'required|numeric|integer|min:-1|max:99999999',
            'balance.recharge_activity_time'    => '',
            'balance.proportion_status'         => 'required|numeric|regex:/^[01]$/',
            'balance.transfer'                  => 'required|numeric|regex:/^[01]$/',
        ];
    }


    private function customAttributes()
    {
        return [
            'balance.recharge'                  => '开启充值',
            'balance.recharge_activity'         => '充值活动',
            'balance.recharge_activity_fetter'  => '会员参与充值活动次数',
            'recharge_activity_time.start'      => '充值活动开始时间',
            'recharge_activity_time.end'        => '充值活动开始时间',
            'balance.proportion_status'         => '充值赠送类型',
            'balance.transfer'                  => '转让开关',
        ];
    }
}
