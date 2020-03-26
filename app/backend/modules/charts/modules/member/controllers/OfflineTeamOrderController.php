<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/13 下午2:48
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\charts\modules\member\controllers;

use app\common\helpers\PaginationHelper;
use Illuminate\Support\Facades\DB;
use app\common\components\BaseController;

class OfflineTeamOrderController extends BaseController
{
    public function index()
    {
        $search = \YunShop::request()->search;
        $pageSize = 10;
        if ($search['member_id']) {
            $sql = '(select child_id, member_id, count(child_id) as team_next_count, count(( select 1 from '. DB::getTablePrefix() .'yz_order where '. DB::getTablePrefix() .'yz_order.uid = '. DB::getTablePrefix() .'yz_member_children.child_id and '. DB::getTablePrefix() .'yz_order.`status` >= 1 limit 1 )) as pay_count, sum(( select sum(goods_total) from '. DB::getTablePrefix() .'yz_order where '. DB::getTablePrefix() .'yz_member_children.child_id = '. DB::getTablePrefix() .'yz_order.uid and '. DB::getTablePrefix() .'yz_order.`status` >= 1 )) as order_goods_total, sum(( select sum(price) from '. DB::getTablePrefix() .'yz_order where '. DB::getTablePrefix() .'yz_member_children.child_id = '. DB::getTablePrefix() .'yz_order.uid and '. DB::getTablePrefix() .'yz_order.`status` >= 1 )) as order_price, '. DB::getTablePrefix() .'mc_members.uid, avatar, nickname from '. DB::getTablePrefix() .'yz_member_children inner join '. DB::getTablePrefix() .'mc_members on '. DB::getTablePrefix() .'yz_member_children.member_id = '. DB::getTablePrefix() .'mc_members.uid where '. DB::getTablePrefix() .'yz_member_children.uniacid = ' . \YunShop::app()->uniacid .' and '. DB::getTablePrefix() .'yz_member_children.member_id='. $search['member_id'] .' group by '. DB::getTablePrefix() .'yz_member_children.member_id order by `team_next_count` desc) as cc';
        } elseif ($search['member_info']) {
            $sql = '(select child_id, member_id, count(child_id) as team_next_count, count(( select 1 from '. DB::getTablePrefix() .'yz_order where '. DB::getTablePrefix() .'yz_order.uid = '. DB::getTablePrefix() .'yz_member_children.child_id and '. DB::getTablePrefix() .'yz_order.`status` >= 1 limit 1 )) as pay_count, sum(( select sum(goods_total) from '. DB::getTablePrefix() .'yz_order where '. DB::getTablePrefix() .'yz_member_children.child_id = '. DB::getTablePrefix() .'yz_order.uid and '. DB::getTablePrefix() .'yz_order.`status` >= 1 )) as order_goods_total, sum(( select sum(price) from '. DB::getTablePrefix() .'yz_order where '. DB::getTablePrefix() .'yz_member_children.child_id = '. DB::getTablePrefix() .'yz_order.uid and '. DB::getTablePrefix() .'yz_order.`status` >= 1 )) as order_price, '. DB::getTablePrefix() .'mc_members.uid, avatar, nickname from '. DB::getTablePrefix() .'yz_member_children inner join '. DB::getTablePrefix() .'mc_members on '. DB::getTablePrefix() .'yz_member_children.member_id = '. DB::getTablePrefix() .'mc_members.uid where '. DB::getTablePrefix() .'yz_member_children.uniacid = ' . \YunShop::app()->uniacid .' and '. DB::getTablePrefix() .'mc_members.nickname like '.'\'%'.$search['member_info'].'\''.' group by '. DB::getTablePrefix() .'yz_member_children.member_id order by `team_next_count` desc) as cc';
        } else {
            $sql = '(select child_id, member_id, count(child_id) as team_next_count, count(( select 1 from '. DB::getTablePrefix() .'yz_order where '. DB::getTablePrefix() .'yz_order.uid = '. DB::getTablePrefix() .'yz_member_children.child_id and '. DB::getTablePrefix() .'yz_order.`status` >= 1 limit 1 )) as pay_count, sum(( select sum(goods_total) from '. DB::getTablePrefix() .'yz_order where '. DB::getTablePrefix() .'yz_member_children.child_id = '. DB::getTablePrefix() .'yz_order.uid and '. DB::getTablePrefix() .'yz_order.`status` >= 1 )) as order_goods_total, sum(( select sum(price) from '. DB::getTablePrefix() .'yz_order where '. DB::getTablePrefix() .'yz_member_children.child_id = '. DB::getTablePrefix() .'yz_order.uid and '. DB::getTablePrefix() .'yz_order.`status` >= 1 )) as order_price, '. DB::getTablePrefix() .'mc_members.uid, avatar, nickname from '. DB::getTablePrefix() .'yz_member_children inner join '. DB::getTablePrefix() .'mc_members on '. DB::getTablePrefix() .'yz_member_children.member_id = '. DB::getTablePrefix() .'mc_members.uid where '. DB::getTablePrefix() .'yz_member_children.uniacid = ' . \YunShop::app()->uniacid . ' group by '. DB::getTablePrefix() .'yz_member_children.member_id order by `team_next_count` desc) as cc';
        }
        $list = DB::table(DB::raw($sql))->paginate($pageSize);
        $page = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        return view('charts.member.offline_team_order', [
            'page' => $page,
            'search' => $search,
            'list' => $list,
        ])->render();
    }
}



