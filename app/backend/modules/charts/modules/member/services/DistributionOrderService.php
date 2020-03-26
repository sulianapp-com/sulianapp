<?php

namespace app\backend\modules\charts\modules\member\services;

use app\backend\modules\charts\modules\member\models\DistributionOrder;
use app\common\models\UniAccount;
use Illuminate\Support\Facades\DB;
use app\Jobs\CountCommissionOrderJob;

class DistributionOrderService
{
    public static function getCommissionOrderNum()
    {
    	$uniAccount = UniAccount::getEnable();
        foreach ($uniAccount as $u) {
        	
        	// $u->uniacid = $u->uniacid;

        	$allData = [];
            
        	$group = DB::table('yz_order as order')
        				->distinct('order.uid')
						->where('order.status', 3)
						->where('order.uniacid', $u->uniacid)
						->where('com_order.uniacid', $u->uniacid)
						->leftJoin('yz_commission_order as com_order', 'order.id', '=', 'com_order.ordertable_id')
						->groupBy('order.uid')
						->get();

			foreach ($group as $k => $v) {
				
				$allData['uid']	 = $v['member_id'];
				$allData['uniacid'] = $u->uniacid;
				//团队总人数
				$allData['team_people_num'] = DB::table('yz_member_children')->select('member_id')->where('member_id', $v['member_id'])->where('uniacid', $u->uniacid)->count() ? : 0;

				$allorder = DB::table('yz_order as order')
				->where('order.status', 3)
				->where('order.uniacid', $u->uniacid)
				->where('com_order.uniacid', $u->uniacid)
				->leftJoin('yz_commission_order as com_order', 'order.id', '=', 'com_order.ordertable_id')
				->get(); 

				if ($allorder) {
					//分销订单数
					$allData['commission_order_num'] = 
					// $allorder->where('order.uid', $v['member_id'])->count() ? : 0;
								DB::table('yz_order as order')
									->where('order.status', 3)
									->where('order.uid', $v['member_id'])
									->where('order.uniacid', $u->uniacid)
									->where('com_order.uniacid', $u->uniacid)
									->leftJoin('yz_commission_order as com_order', 'order.id', '=', 'com_order.ordertable_id')
									->count() ? : 0;
					//分销订单业绩
					$allData['commission_order_prices'] = 
						// $allorder->where('order.uid', $v['member_id'])->sum('order.price') ? : 0.00;
						DB::table('yz_order as order')
									->where('order.status', 3)
									->where('order.uid', $v['member_id'])
									->where('order.uniacid', $u->uniacid)
									->where('com_order.uniacid', $u->uniacid)
									->leftJoin('yz_commission_order as com_order', 'order.id', '=', 'com_order.ordertable_id')
									->sum('order.price') ? : 0.00;

					//获取团队下线会员id
					$uids = DB::table('yz_member_children')->select('child_id')->where('member_id', $v['member_id'])->get();
					
					if ($uids) {
						
						$uids = array_column($uids->toArray(), 'child_id');
						//团队分销订单业绩
						$teamOrderPrice = 
						// $allorder->whereIn('order.uid', $uids)->sum('order.price') ? : 0.00;
						DB::table('yz_order as order')
									->where('order.status', 3)
									->whereIn('order.uid', $uids)
									->where('order.uniacid', $u->uniacid)
									->where('com_order.uniacid', $u->uniacid)
									->leftJoin('yz_commission_order as com_order', 'order.id', '=', 'com_order.ordertable_id')
									->sum('order.price') ? : 0.00;

						// dd(DB::table('yz_order as order')->whereIn('uid',$uids)->sum('price'), $uids);
					}
					$allData['team_commission_order_prices'] = $teamOrderPrice ? : 0.00;
				
				} else {

					$allData['commission_order_num'] = 0;
					$allData['commission_order_prices'] = 0.00;
					$allData['team_commission_order_prices'] = 0.00;
				}
				$DistributionOrder = new DistributionOrder;
				$DistributionOrder->updateOrCreate(['uid'=>$v['member_id']], $allData);
			}
        }
    }
}