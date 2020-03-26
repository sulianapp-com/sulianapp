<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/19
 * Time: 下午7:22
 */

namespace app\backend\modules\finance\controllers;


use app\common\components\BaseController;
use app\common\modules\payType\remittance\models\flows\RemittanceAuditFlow;
use app\common\modules\payType\remittance\models\process\RemittanceAuditProcess;
use Illuminate\Database\Eloquent\Builder;


class RemittanceAuditController extends BaseController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {
        return view('finance.remittance.audits', ['data' => json_encode($this->getData())])->render();
    }
    public function ajax(){
        return $this->successJson('成功',$this->getData());
    }
    private function getData(){
        $pageSize = (int)request()->input('pagesize',20);

        /**
         * @var RemittanceAuditFlow $remittanceAuditFlow
         */
        $searchParams = request()->input('searchParams');
        $remittanceAuditFlow = RemittanceAuditFlow::first();
        $processBuilder = RemittanceAuditProcess::where('flow_id', $remittanceAuditFlow->id)->uniacid()->with(['member', 'status', 'remittanceRecord' => function ($query) {
            $query->with('orderPay');
        }]);
        if(!empty(request()->input('status_id'))){
            $processBuilder->where('status_id',request()->input('status_id'));
        }
        $processList = $processBuilder->orderBy('id','desc')->paginate($pageSize)->toArray();
        $processList['pagesize'] = $pageSize;
        //dd($processList);
        //exit;

        $allStatus = $remittanceAuditFlow->allStatus;
        $data = [
            'remittanceAudits' => $processList,
            'allStatus' => $allStatus,
            'searchParams' => $searchParams,
        ];
        return $data;
    }
}