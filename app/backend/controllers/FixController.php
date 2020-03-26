<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/8/8
 * Time: 下午6:52
 */

namespace app\backend\controllers;


use app\common\components\BaseController;
use app\common\models\Income;
use app\common\models\Order;
use app\common\services\member\MemberRelation;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\DB;
use Yunshop\Commission\models\Commission;
use Yunshop\Commission\models\CommissionOrder;
use Yunshop\Commission\services\CommissionOrderService;
use Yunshop\Mryt\job\UpgradeByRegisterJob;
use Yunshop\Mryt\listeners\MemberRelationEventListener;
use Yunshop\Mryt\services\UpgradeService;
use Yunshop\TeamDividend\models\TeamDividendModel;

class FixController extends BaseController
{
    use DispatchesJobs;

    public function errorDividendData()
    {
        $a = DB::table('yz_team_dividend')->select(['yz_team_dividend.uniacid as tuniacid','mc_members.uniacid as uniacid','yz_team_dividend.id' , 'yz_order.id as orderid', 'yz_order.uid', 'yz_team_dividend.order_sn', 'yz_team_dividend.member_id', 'yz_team_dividend.status'])
            ->join('yz_order', 'yz_order.order_sn', '=', 'yz_team_dividend.order_sn')
            ->join('mc_members', 'mc_members.uid', '=', 'yz_team_dividend.member_id')
            ->where(DB::raw('ims_mc_members.uniacid != ims_yz_team_dividend.uniacid'))
            ->orderBy('yz_team_dividend.id', 'asc')
            ->get();
        dump($a);
    }
    public function handleCommissionOrder()
    {

        $handle = 0;
        $success = 0;

        $waitCommissionOrder = CommissionOrder::uniacid()->whereStatus(0)->get();

        $status = \Setting::get('plugin.commission')['settlement_event'] ? 1 : 3;
        if (!$waitCommissionOrder->isEmpty()) {

            foreach ($waitCommissionOrder as $key => $commissionOrder) {

                $orderModel = Order::uniacid()->whereId($commissionOrder->ordertable_id)->first();

                if ($orderModel->status >= $status) {

                    $handle += 1;
                    $commissionOrder->status = 1;
                    if ($status == 1) {
                        $commissionOrder->recrive_at = $orderModel->pay_time;
                    } else {
                        $commissionOrder->recrive_at = $orderModel->finish_time;
                    }

                    if ($commissionOrder->save()) {
                        $success += 1;
                    }
                }
                unset($orderModel);
            }
        }

        echo "分销订单未结算总数：{$waitCommissionOrder->count()}，已完成订单数：{$handle}, 执行成功数：{$success}";
    }

    public function fixTeam()
    {
        $search_date = strtotime('2018-10-25 12:00:00');
        $error = [];
        $tmp      = [];
        $pos      = [];

        $res = DB::table('yz_team_dividend as t')
            ->select(['t.id' , 'o.id as orderid', 'o.uid', 't.order_sn', 't.member_id', 't.status'])
            ->join('yz_order as o', 'o.order_sn', '=', 't.order_sn')
            ->where('t.created_at', '>', $search_date)
            ->orderBy('t.id', 'asc')
            ->get();

        if (!$res->isEmpty()) {
            foreach ($res as $key => $rows) {
                if (!$tmp[$rows['orderid']]) {
                    // $pos = [$rows->member_id => $key];

                    $tmp[$rows['orderid']] = [
                        'id'    => $rows['id'],
                        'order_id' => $rows['orderid'],
                        'uid' => $rows['uid'],
                        'order_sn' => $rows['order_sn'],
                        'parent_id' => $rows['member_id'],
                        'status' => $rows['status'],
                    ];

                    file_put_contents(storage_path('logs/team_fix.log'), print_r($tmp, 1), FILE_APPEND);
                } else {
//                    $k = $pos[$rows->member_id];
//                    $tmp[$k]['member_id'][] = $rows->member_id;
                }
            }
        }

        //订单会员->关系链 不匹配
        foreach ($tmp as $k => $v) {
            $total = DB::table('yz_member')
                ->where('member_id', '=', $v['uid'])
                ->where('parent_id', '=', $v['parent_id'])
                ->count();

            if (0 == $total) {
                $error[] = $v;

                file_put_contents(storage_path('logs/team_fix_error.log'), print_r($v, 1), FILE_APPEND);
            }
        }

        collect($error)->each(function ($item) {
            if (0 == $item['status']) {
                $model = Order::find($item['order_id']);

                if (!is_null($model)) {
                    DB::transaction(function () use ($item, $model) {
                        DB::table('yz_team_dividend')
                            ->where('order_sn', '=', $item['order_sn'])
                            ->delete();

                        DB::table('yz_order_plugin_bonus')
                            ->where('order_id', '=', $item['order_id'])
                            ->where('table_name', '=', 'yz_team_dividend')
                            ->delete();

                        (new \Yunshop\TeamDividend\Listener\OrderCreatedListener)->fixOrder($model);

                        file_put_contents(storage_path('logs/team_fix_del.log'), print_r($item, 1), FILE_APPEND);
                    });
                }
            }
        });

        echo '数据修复ok';
    }

    public function fixArea()
    {
        $search_date = strtotime('2018-10-25 12:00:00');
        $error = [];
        $tmp      = [];

        $res = DB::table('yz_area_dividend as t')
            ->select(['t.id' , 'o.id as orderid', 'o.uid', 't.order_sn', 't.member_id', 't.status'])
            ->join('yz_order as o', 'o.order_sn', '=', 't.order_sn')
            ->where('t.created_at', '>', $search_date)
            ->orderBy('t.id', 'asc')
            ->get();

        if (!$res->isEmpty()) {
            foreach ($res as $key => $rows) {
                if (!$tmp[$rows['orderid']]) {
                    // $pos = [$rows->member_id => $key];

                    $tmp[$rows['orderid']] = [
                        'id'    => $rows['id'],
                        'order_id' => $rows['orderid'],
                        'uid' => $rows['uid'],
                        'order_sn' => $rows['order_sn'],
                        'parent_id' => $rows['member_id'],
                        'status' => $rows['status'],
                    ];

                    file_put_contents(storage_path('logs/area_fix.log'), print_r($tmp, 1), FILE_APPEND);
                }
            }
        }

//        //订单会员->关系链 不匹配
//        foreach ($tmp as $k => $v) {
//            $total = DB::table('yz_member')
//                ->where('member_id', '=', $v['uid'])
//                ->where('parent_id', '=', $v['parent_id'])
//                ->count();
//
//            if (0 == $total) {
//                $error[] = $v;
//
//                file_put_contents(storage_path('logs/area_fix_error.log'), print_r($v, 1), FILE_APPEND);
//            }
//        }

        collect($tmp)->each(function ($item) {
            if (0 == $item['status']) {
                $model = Order::find($item['order_id']);

                DB::transaction(function () use ($item, $model) {
                    DB::table('yz_area_dividend')
                        ->where('order_sn', '=', $item['order_sn'])
                        ->delete();

                    DB::table('yz_order_plugin_bonus')
                        ->where('order_id', '=', $item['order_id'])
                        ->where('table_name', '=', 'yz_area_dividend')
                        ->delete();

                    (new \Yunshop\AreaDividend\Listener\OrderCreatedListener)->fixOrder($model);

                    file_put_contents(storage_path('logs/area_fix_del.log'), print_r($item, 1), FILE_APPEND);
                });
            }
        });

        echo '数据修复ok';
    }

    public function fixIncome()
    {
        $count = 0;
        $income = Income::whereBetween('created_at', [1539792000,1541433600])->get();
        foreach ($income as $value) {
            $pattern1 = '/\\\u[\d|\w]{4}/';
            preg_match($pattern1, $value->detail, $exists);
            if (empty($exists)) {
                $pattern2 = '/(u[\d|\w]{4})/';
                $value->detail = preg_replace($pattern2, '\\\$1', $value->detail);
                $value->save();
                $count++;
            }
        }
        echo "修复了{$count}条";
    }

    public function mr()
    {
        $m = new MemberRelationEventListener();

        $parent_id = 6080;

        $m->fixRelation($parent_id);
    }

    public function ma()
    {
        $m = new MemberRelationEventListener();

        $parent_id = 6080;

        $m->fixAward($parent_id);
    }

    public function changeField()
    {
        $sql1 = 'ALTER TABLE `' . DB::getTablePrefix() . 'users_permission` MODIFY `modules` text NULL';
        $sql2 = 'ALTER TABLE `' . DB::getTablePrefix() . 'users_permission` MODIFY `templates` text NULL';

        try {
            DB::select($sql1);
            DB::select($sql2);
            echo '数据已修复';
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function handleTeamOrder()
    {
        $handle = 0;
        $success = 0;
        $status = \Setting::get('plugin.team_dividend')['settlement_event'] ? 1 : 3;

        $waitTeamOrder = \Yunshop\TeamDividend\models\TeamDividendModel::uniacid()->whereStatus(0)->get();

        if (!$waitTeamOrder->isEmpty()) {

            foreach ($waitTeamOrder as $key => $teamOrder) {

                $orderModel = \app\common\models\Order::uniacid()->where('order_sn',$teamOrder->order_sn)->first();


                if ($orderModel->status >= $status) {
                    $handle += 1;
                    $teamOrder->recrive_at = strtotime($orderModel->pay_time);

                    if ($teamOrder->save()) {
                        $success += 1;
                    }
                }
                unset($orderModel);
            }
        }

        echo "经销商订单未结算总数：{$waitTeamOrder->count()}，已完成订单数：{$handle}, 执行成功数：{$success}";
    }

    public function fixCommissionAmount()
    {
        $CommissionOrder = CommissionOrder::uniacid()->whereNull('commission_amount')->get();
        $set = \Setting::get('plugin.commission');
        $count = 0;
        foreach ($CommissionOrder as $commission) {
            $orderModel = Order::find($commission->ordertable_id);
            $orderGoods = $orderModel->hasManyOrderGoods;
            $commissionAmount = 0;
            $formula = '';
            foreach ($orderGoods as $key => $og) {

                //获取商品分销设置信息
                $commissionGoods = Commission::getGoodsById($og->goods_id)->first();
                if (!$commissionGoods->is_commission) {
                    continue;
                }

                if ($commissionGoods) {
                    if ($commissionGoods['has_commission'] == '1') {
                        //商品独立佣金
                        $commissionAmount += $og['payment_amount']; //分佣计算金额
                        $formula .= "+商品独立佣金";//分佣计算方式
                    } else {
                        $countAmount = CommissionOrderService::getCountAmount($orderModel, $og, '', $set);
                        $commissionAmount += $countAmount['amount'];
                        $formula = $countAmount['method'];
                    }
                }
            }
            $commission->commission_amount = $commissionAmount;
            $commission->formula = $formula;

            $commission->save();
            $count++;
        }
        echo '修改了'.$count.'条信息';
    }

    public function fixNotCommission()
    {
        $order_sn = \YunShop::request()->order_sn;
        $order = Order::uniacid()->where('order_sn', $order_sn)->first();
        $commission_order = CommissionOrder::uniacid()->where('ordertable_id', $order->id)->first();
        if ($commission_order) {
            echo '已有这条分红';
        } else {
            $result = (new \Yunshop\Commission\Listener\OrderCreatedListener())->handler($order);
            $commission_order = CommissionOrder::uniacid()->where('ordertable_id', $order->id)->first();
            if ($commission_order) {
                echo '成功';
            } else {
                echo '不成功，请检查设置是否正确，一定绝对必须要检查清楚！！！！！！如果正确？！那就服务器有问题，非常难受';
            }
        }


    }

    public function fixNotTeam()
    {
        $order_sn = \YunShop::request()->order_sn;
        $order = Order::uniacid()->where('order_sn', $order_sn)->first();
        $team_order = TeamDividendModel::uniacid()->where('order_sn', $order_sn)->first();
        if ($team_order) {
            echo '已有这条分红';
        } else {
            (new \Yunshop\TeamDividend\Listener\OrderCreatedListener())->handle($order);
            $team_order = TeamDividendModel::uniacid()->where('order_sn', $order_sn)->first();
            if ($team_order) {
                echo '成功';
            } else {
                echo '不成功，请检查设置是否正确，一定绝对必须要检查清楚！！！！！！如果正确？！那就服务器有问题，非常难受';
            }
        }

    }

    public function fixChangeMemberRelation()
    {
        $member_relation = new MemberRelation();

        $a = [
            [2186, 66]
        ];


        foreach ($a as $item) {
            $member_relation->build($item[0], $item[1]);
        }
        echo 'ok';
    }

}