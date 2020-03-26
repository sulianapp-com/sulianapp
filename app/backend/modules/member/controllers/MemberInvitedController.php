<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/2/13
 * Time: 15:41
 */

namespace app\backend\modules\member\controllers;


use app\common\components\BaseController;
use app\common\models\member\MemberInvitationCodeLog;
use app\common\helpers\PaginationHelper;
use app\common\services\ExportService;
use app\common\models\MemberShopInfo;


class MemberInvitedController extends BaseController
{
    public function index()
    {
        $search = \YunShop::request()->search;

        $list =  MemberInvitationCodeLog::
        searchLog($search)
        ->orderBy('id', 'desc')
        ->groupBy('member_id')
        ->paginate()
        ->toArray();

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);

        return view('member.invited', ['list'=>$list, 'pager'=>$pager, 'search'=>$search])->render();
    }

    public function export()
    {
        $member_builder = MemberInvitationCodeLog::searchLog(\YunShop::request()->search)->orderBy('id', 'desc');
        $export_page = request()->export_page ? request()->export_page : 1; 

        $export_model = new ExportService($member_builder, $export_page);
        $file_name = date('Ymdhis', time()) . '邀请码使用情况导出';

        $export_data[0] = ['ID', '邀请人id', '被邀请人id', '邀请码', '注册时间'];

        $list = $export_model->builder_model->toArray();

        if ($list) {

            foreach ($list as $key => $item) {
                $export_data[$key + 1] = [$item['id'], $item['mid'], $item['member_id'], $item['invitation_code'],
                    $item['created_at']
                ];
            }
            \Excel::create($file_name, function ($excel) use ($export_data) {
                // Set the title
                $excel->setTitle('Office 2005 XLSX Document');

                // Chain the setters
                $excel->setCreator('芸众商城')
                    ->setLastModifiedBy("芸众商城")
                    ->setSubject("Office 2005 XLSX Test Document")
                    ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
                    ->setKeywords("office 2005 openxml php")
                    ->setCategory("report file");

                $excel->sheet('info', function ($sheet) use ($export_data) {
                    $sheet->rows($export_data);
                });
            })->export('xls');

        } else {
            return $this->message('暂无数据', yzWebUrl('member.member-invited.index'));
        }
    }
}