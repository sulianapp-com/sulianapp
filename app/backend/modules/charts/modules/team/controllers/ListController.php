<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2019/1/11
 * Time: 11:49
 */
namespace app\backend\modules\charts\modules\team\controllers;



use app\backend\modules\member\models\MemberChildren;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;

class ListController extends BaseController
{
   public function index(){
       $search = \YunShop::request()->search;
       if(!$search){
           $time=time();
           $nowyear = date('Y',$time);
           $nowmonth = date('n',$time);
           if($nowmonth == 1){
               $nowyear = $nowyear -1;
               $nowmonth =12 ;
           }
           $search=[
               'year' =>$nowyear,
                   'month' =>$nowmonth
           ];
       }
       $pageSize = 20;
       $uniacid = \YunShop::app()->uniacid;
       $list = MemberChildren::getTeamCount($search,$uniacid) ->paginate($pageSize);
       $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

       return view('charts.team.list', [
           'list' => $list->toarray(),
           'pager' => $pager,
           'total' => $list->total(),
           'search' => $search,
       ])->render();
   }

   public function export(){
       $search = \YunShop::request()->search;
       if(!$search){
           $time=time();
           $nowyear = date('Y',$time);
           $nowmonth = date('n',$time);
           if($nowmonth == 1){
               $nowyear = $nowyear -1;
               $nowmonth =12 ;
           }
           $search=[
               'year' =>$nowyear,
               'month' =>$nowmonth
           ];
       }
       $file_name = date('Ymdhis', time()) . $search['year'].'年'.$search['month'].'月一二级团队统计导出';


       $uniacid = \YunShop::app()->uniacid;
       $list = MemberChildren::getTeamCount($search,$uniacid)
           ->get()
           ->toArray();

       $export_data[0] = [
           '排行',
           '会员ID',
           '会员',
           '会员姓名',
           '会员手机',
           '一二级团队人数',
           '一二级团队订单总数',
           '一二级团队订单总额',
       ];

       foreach ($list as $key => $item) {
           $export_data[$key + 1] = [
               $key + 1,
               $item['member_id'],
               $item['nickname'],
               $item['realname'],
               $item['mobile'],
               $item['level_num']?:0,
               $item['order_all']?:0,
               $item['price_all']?$item['price_all']."元":0.00."元"
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
}