<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/12
 * Time: 9:09
 */

namespace app\payment\controllers;

use app\common\components\BaseController;
use app\common\models\AccountWechats;
use app\common\modules\yop\sdk\Util\YopSignUtils;
use Illuminate\Support\Facades\DB;
use Yunshop\YopPay\models\SubMerchant;


class YopmerchantController extends BaseController
{

    protected $set;

    protected  $parameters;

    public function __construct()
    {
        parent::__construct();

        if (!app('plugins')->isEnabled('yop-pay')) {
            echo 'Not turned on yop pay';
            exit();
        }

        $this->set = $this->getMerchantNo();

        if (empty(\YunShop::app()->uniacid)) {
            \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $this->set['uniacid'];
            AccountWechats::setConfig(AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid));
        }

        $this->init();
    }

    private function init()
    {
        $yop_data = $_REQUEST['response'];
        if ($yop_data) {
            $response = YopSignUtils::decrypt($yop_data, $this->set['private_key'], $this->set['yop_public_key']);
            $this->parameters = json_decode($response, true);
        }
    }

    protected function getMerchantNo()
    {
        //\Log::debug('--------------易宝入网参数--------------', $_REQUEST);

        $app_key = $_REQUEST['customerIdentification'];
        $merchant_no = substr($app_key,  strrpos($app_key, 'OPR:')+4);

//        $model = DB::table('yz_yop_setting')->where('parent_merchant_no', $merchant_no)->first();
//
//        if ($model) {
//            return [
//                'uniacid' => $model['uniacid'],
//                'merchant_number' => $model['parent_merchant_no'],
//                'private_key' => $model['private_key'],
//                'yop_public_key' => $model['yop_public_key'],
//            ];
//        }

        $model = DB::table('yz_yop_setting')->where('merchant_no', $merchant_no)->first();

        if (empty($model)) {
            exit('商户不存在');
        }
        return [
            'uniacid' => $model['uniacid'],
            'merchant_number' => $model['merchant_no'],
            'private_key' => $model['son_private_key'],
            'yop_public_key' => $model['yop_public_key'],
        ];
    }


    //子商户入网
    public function notifyUrl()
    {
        \Log::debug('--------------易宝入网--------------', $this->parameters);

        $this->yopResponse('子商户入网', $this->parameters, 'sub');


        $son = SubMerchant::withoutGlobalScope('is_son')->where('requestNo', $this->parameters['requestNo'])->first();

        if (empty($son)) {
            exit('Merchant does not exist');
        }

        $status = $this->merNetInStatus();

        $son->status = $status;
        $son->externalId = $this->parameters['externalId'];
        $son->remark = $this->parameters['remark'] ?: '';
        $bool = $son->save();
        if ($bool) {
            echo 'SUCCESS';
            exit();
        } else {
            echo '保存出错';
            exit();
        }
    }

    protected function merNetInStatus()
    {
        $status = SubMerchant::INVALID;
        if (!empty($this->parameters['merNetInStatus'])) {
            switch ($this->parameters['merNetInStatus']) {
                case 'PROCESS_SUCCESS': //审核通过
                    $status = SubMerchant::PROCESS_SUCCESS;
                    break;
                case 'PROCESS_REJECT': //审核拒绝
                    $status = SubMerchant::PROCESS_REJECT;
                    break;
                case 'PROCESS_BACK': //审核回退
                    $status = SubMerchant::PROCESS_BACK;
                    break;
                case 'PROCESSING_PRODUCT_INFO_SUCCESS': //审核中-产品提前开通
                    $status = SubMerchant::PROCESSING_PRODUCT_INFO_SUCCESS;
                    break;
                default:
                    break;
            }
        }

        return $status;
    }

    //聚合报备
    public function backUrl()
    {
        \Log::debug('-------------聚合报备---------------', $this->parameters);

        $this->yopResponse('聚合报备', $this->parameters, 'back');

        $son = SubMerchant::withoutGlobalScope('is_son')->isSon(0)->where('merchantNo', $this->parameters['merchantNo'])->first();

        if (empty($son)) {
            exit('Merchant does not exist');
        }

        $report_status = $this->reportStatusCode();

        $son->report_status = $report_status;
        $bool = $son->save();
        if ($bool) {
            echo 'SUCCESS';
            exit();
        } else {
            echo '保存出错';
            exit();
        }
    }

    protected function reportStatusCode()
    {
            switch ($this->parameters['reportStatusCode']) {
                //报备成功
                case '':
                case 'NULL':
                case '0000':
                $report_status = SubMerchant::BACK_SUCCESS;
                    break;
                //处理中
                case '1111':
                case '1112':
                case '3333':
                case '710001':
                $report_status = SubMerchant::BACK_WAIT;
                    break;
                //失败
                default:
                    $report_status = SubMerchant::BACK_FAIL;
                    break;
            }

        return $report_status;
    }

    protected function yopLog($desc,$error,$data)
    {
        \Yunshop\YopPay\common\YopLog::yopLog($desc, $error,$data);
    }

    protected function yopResponse($desc,$params, $type = 'unify')
    {
        \Yunshop\YopPay\common\YopLog::yopResponse($desc, $params, $type);
    }
}