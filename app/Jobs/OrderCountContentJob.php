<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/9/19
 * Time: 下午3:37
 */

namespace app\Jobs;

use app\backend\modules\charts\models\OrderIncomeCount;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrderCountContentJob implements  ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $orderModel;
    protected $countModel;

    public function __construct($orderModel)
    {
        $this->orderModel = $orderModel;
    }

    public function handle()
    {
        $data = [
            'uniacid' => $this->orderModel->uniacid,
            'price' => $this->orderModel->price,
            'uid' => $this->orderModel->uid,
            'order_sn' => $this->orderModel->order_sn,
            'order_id' => $this->orderModel->id,
            'status' => $this->orderModel->status,
            'plugin_id' => $this->orderModel->plugin_id,
            'dispatch_price' => $this->orderModel->dispatch_price,
            'shop_name' => $this->orderModel->shop_name,
            'cost_price' => $this->orderModel->cost_amount,
            'day_time' => Carbon::today()->getTimestamp(),
        ];
        $data['address'] = $this->address();
        $data['buy_name'] = $this->buyName();
        $parent = $this->referrerName();
        $data['parent_id'] = $parent['parent_id'];
        $data['parent_name'] = $parent['nickname'];

//        $build = OrderIncomeCount::where('order_id',$this->orderModel->id)->first();
//        if ($build) {
//            return $build;
//        } else {
        $build = OrderIncomeCount::create($data);
//        }
        return $build;
    }



    public function address()
    {
        $build = DB::table('yz_order_address')
            ->select()
            ->where('order_id', $this->orderModel->id);
        $content = $build->first()['address'];
        if (empty($content)) {
            return '';
        }

        return $content;
    }

    public function buyName()
    {
        $build = DB::table('mc_members')
            ->select()
            ->where('uid', $this->orderModel->uid);
        $content = $build->first()['nickname'];
        if (empty($content)) {
            return '';
        }
        return $content;
    }

    public function referrerName()
    {
        $referrerTable = DB::table('yz_member')
            ->select()
            ->where('member_id', $this->orderModel->uid);
        $parent_id = $referrerTable->first()['parent_id'];
        if ($parent_id) {
            $build = DB::table('mc_members')
                ->select()
                ->where('uid', $parent_id);
            $content['nickname'] = $build->first()['nickname'];
            $content['parent_id'] = $parent_id;
        } else {
            $content['nickname'] = '总店';
            $content['parent_id'] = 0;
        }
        return $content;
    }

    //记录商家名称
//    public function shopName()
//    {
//        if ($this->orderModel->is_plugin) {
//            $supplierTable = DB::table('yz_supplier_order')
//                ->select()
//                ->where('order_id', $this->orderModel->id);
//            $supplier_id = $supplierTable->first()['supplier_id'];
//            $build = DB::table('yz_supplier')
//                ->select()
//                ->where('id', $supplier_id);
//            $content = $build->first()['username'];
//            if (empty($content)) {
//                $content = '供应商';
//            }
//
//        } elseif ($this->orderModel->plugin_id == 31) {
//            $cashierTable = DB::table('yz_plugin_cashier_order')
//                ->select()
//                ->where('order_id', $this->orderModel->id);
//            $cashier_id = $cashierTable->first()['cashier_id'];
//            $build = DB::table('yz_store')
//                ->select()
//                ->where('cashier_id', $cashier_id);
//            $content = $build->first()['store_name'];
//            if (empty($content)) {
//                $content = '收银台';
//            }
//
//        } elseif ($this->orderModel->plugin_id == 32) {
//            $storeTable = DB::table('yz_plugin_store_order')
//                ->select()
//                ->where('order_id', $this->orderModel->id);
//            $store_id = $storeTable->first()['store_id'];
//            $build = DB::table('yz_store')
//                ->select()
//                ->where('id', $store_id);
//            $content = $build->first()['store_name'];
//            if (empty($content)) {
//                return '门店';
//            }
//
//        } else {
//            $content = '平台自营';
//        }
//        return $content;
//    }

    //记录成本价
//    public function costPrice()
//    {
//        $build = DB::table('yz_order_goods')
//            ->select()
//            ->where('order_id', $this->orderModel->id)
//            ->sum('goods_cost_price');
//        if ($this->orderModel->plugin_id == 32) {
//
//            $order = DB::table('yz_plugin_store_order')
//                ->select()
//                ->where('order_id', $this->orderModel->id)
//                ->first();
//            $cost_price = $order['amount'];
//        } else if ($this->orderModel->plugin_id == 31) {
//
//            $order = DB::table('yz_plugin_cashier_order')
//                ->select()
//                ->where('order_id', $this->orderModel->id)
//                ->first();
//            $cost_price = $order['amount'];
//        } else {
//            $cost_price = $build;
//        }
//        return $cost_price;
//    }
}