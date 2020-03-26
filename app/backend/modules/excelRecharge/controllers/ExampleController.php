<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019-06-19
 * Time: 16:43
 */

namespace app\backend\modules\excelRecharge\controllers;


use app\common\components\BaseController;

class ExampleController extends BaseController
{
    public function index()
    {
        $id = request()->input('id');
        if($id == 1){
            $exportData['0'] = ["会员ID", "充值数量"];
        }else{
            $exportData['0'] = ["手机号", "充值数量"];
        }

        \Excel::create('批量充值模板', function ($excel) use ($exportData) {
            $excel->setTitle('Office 2005 XLSX Document');
            $excel->setCreator('芸众商城');
            $excel->setLastModifiedBy("芸众商城");
            $excel->setSubject("Office 2005 XLSX Test Document");
            $excel->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.");
            $excel->setKeywords("office 2005 openxml php");
            $excel->setCategory("report file");
            $excel->sheet('info', function ($sheet) use ($exportData) {
                $sheet->rows($exportData);
            });
        })->export('xls');
    }

}
