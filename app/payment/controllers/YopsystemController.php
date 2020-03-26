<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/1
 * Time: 10:09
 */

namespace app\payment\controllers;

use app\common\components\BaseController;
use app\common\modules\yop\sdk\Util\YopSignUtils;
use Yunshop\YopSystem\common\YopLog;
use Yunshop\YopSystem\models\YopSystemMerchant;
use app\common\models\AccountWechats;
use Illuminate\Support\Facades\DB;

class YopsystemController extends BaseController
{

    protected $set;

    protected  $parameters;

    public function __construct()
    {
        parent::__construct();

        if (!app('plugins')->isEnabled('yop-system')) {
            echo 'Not turned on yop system';
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
        \Log::debug('--------------易宝入网参数--------------', $_REQUEST);

        $app_key = $_REQUEST['customerIdentification'];
        $merchant_no = substr($app_key,  strrpos($app_key, 'OPR:')+4);

        $model = DB::table('yz_yop_system')->where('parent_merchant_no', $merchant_no)->first();

        if (empty($model)) {
            exit('商户不存在');
        }

        return $model;
    }

    //子商户入网
    public function notifyUrl()
    {
        \Log::debug('--------------易宝入网--------------', $this->parameters);

        $this->yopResponse('子商户入网:'.$this->parameters['requestNo'], $this->parameters);


        $son = YopSystemMerchant::where('requestNo', $this->parameters['requestNo'])->first();

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
        $status = YopSystemMerchant::INVALID;
        if (!empty($this->parameters['merNetInStatus'])) {
            switch ($this->parameters['merNetInStatus']) {
                case 'PROCESS_SUCCESS': //审核通过
                    $status = YopSystemMerchant::PROCESS_SUCCESS;
                    break;
                case 'PROCESS_REJECT': //审核拒绝
                    $status = YopSystemMerchant::PROCESS_REJECT;
                    break;
                case 'PROCESS_BACK': //审核回退
                    $status = YopSystemMerchant::PROCESS_BACK;
                    break;
                case 'PROCESSING_PRODUCT_INFO_SUCCESS': //审核中-产品提前开通
                    $status = YopSystemMerchant::PROCESSING_PRODUCT_INFO_SUCCESS;
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

        $this->yopResponse('聚合报备:'.$this->parameters['merchantNo'], $this->parameters);

        $son = YopSystemMerchant::where('merchantNo', $this->parameters['merchantNo'])->first();

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
                $report_status = YopSystemMerchant::BACK_SUCCESS;
                break;
            //处理中
            case '1111':
            case '1112':
            case '3333':
            case '710001':
                $report_status = YopSystemMerchant::BACK_WAIT;
                break;
            //失败
            default:
                $report_status = YopSystemMerchant::BACK_FAIL;
                break;
        }

        return $report_status;
    }

    protected function yopLog($desc, $data)
    {
        YopLog::yopLog($desc, $data);
    }

    protected function yopResponse($desc, $params)
    {
        YopLog::yopResponse($desc, $params);
    }

}