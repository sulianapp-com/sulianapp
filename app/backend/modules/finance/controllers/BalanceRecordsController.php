<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/11 下午9:25
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\finance\controllers;


use app\backend\modules\finance\models\Balance;
use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\PaginationHelper;
use app\common\services\credit\ConstService;
use app\common\services\member\group\GroupService;
use app\common\services\member\level\LevelService;

class BalanceRecordsController extends BaseController
{
    const PAGE_SIZE = 20;

    public function index()
    {
        $records = Balance::records();
        $search = $this->getPostSearch();
        if ($search) {
            //dd($search);
            $records = $records->search($search)->searchMember($search);
        }

        $pageList = $records->orderBy('created_at','desc')->paginate(static::PAGE_SIZE);
        $page = PaginationHelper::show($pageList->total(),$pageList->currentPage(),$pageList->perPage());

        return view('finance.balance.balanceRecords',[
            'pageList'          => $pageList,
            'page'              => $page,
            'search'            => $search,
            'shopSet'           => $this->getShopSet(),
            'sourceName'        => $this->getServiceType(),
            'memberLevels'      => $this->getMemberList(),
            'memberGroups'      => $this->getMemberGroup(),
        ])->render();

    }

    public function export()
    {
        $file_name = date('Ymdhis', time()) . '余额明细导出';

        $search = $this->getPostSearch();
        $list = Balance::records()->search($search)->searchMember($search)->get();

        $export_data[0] = ['时间', '会员ID', '会员姓名', '会员手机号', '会员等级', '会员分组', '订单号', '业务类型', '收入／支出','变动前余额','变动余额', '变动后余额','备注'];

        $shopSet = $this->getShopSet();


        foreach ($list as $key => $item) {

            if ($item->member) {

                $member_id          = $item->member->uid;
                $member_name        = $item->member->realname ?: $item->member->nickname;
                $member_mobile      = $item->member->mobile;
                $member_level       = $shopSet['level_name'];
                $member_group       = '无分组';

                if ($item->member->yz_member->group) {
                    $member_group       = $item->member->yz_member->group->group_name ?: '无分组';
                }
                if ($item->member->yz_member->level) {
                    $member_level       = $item->member->yz_member->level->level_name ?: $shopSet['level_name'];
                }


            } else {
                $member_id          = '';
                $member_name        = '';
                $member_mobile      = '';
                $member_level       = $shopSet['level_name'];
                $member_group       = '无分组';
            }

            $export_data[$key + 1] = [
                $item->created_at,
                $member_id,
                $member_name,
                $member_mobile,
                $member_level,
                $member_group,
                $item->serial_number,
                $item->service_type_name,
                $item->type_name,
                $item->old_money,
                $item->change_money,
                $item->new_money,
                $item->remark
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
    }

    private function getPostSearch()
    {
        return \YunShop::request()->search;
    }

    private function getShopSet()
    {
        return Setting::get('shop.member');
    }

    private function getServiceType()
    {
        return (new ConstService(''))->sourceComment();
    }

    private function getMemberList()
    {
        return LevelService::getMemberLevelList();
    }

    private function getMemberGroup()
    {
        return GroupService::getMemberGroupList();
    }




}