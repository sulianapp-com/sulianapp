<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/30
 * Time: 下午9:07
 */

namespace app\frontend\modules\finance\controllers;

use app\common\components\ApiController;
use app\common\components\BaseController;
use app\common\models\Store;
use app\frontend\modules\finance\models\Withdraw;
use Yunshop\Hotel\common\models\CashierOrder;
use Yunshop\Hotel\common\models\Hotel;
use Yunshop\Hotel\common\models\HotelOrder;
use Yunshop\StoreCashier\common\models\StoreOrder;
use app\common\helpers\PaginationHelper;
use Yunshop\StoreCashier\frontend\store\models\Goods;
//use Yunshop\StoreCashier\common\models\CashierOrder;

class WithdrawController extends ApiController
{
    public $pageSize = 15;

    public function withdrawLog()
    {
        $status = \YunShop::request()->status;
        $request = Withdraw::getWithdrawLog($status)->orderBy('created_at', 'desc')->paginate($this->pageSize);
        if ($request) {
            return $this->successJson('获取数据成功!', $request->toArray());
        }
        return $this->errorJson('未检测到数据!');
    }

    public function withdrawInfo()
    {
        $id = \YunShop::request()->id;
        $request = Withdraw::getWithdrawInfoById($id)->first();

        if ($request) {

            return $this->successJson('获取数据成功!', $request->toArray());
        }
        return $this->errorJson('未检测到数据!');
    }

    /**
     * 提成列表
     */
    public function withdrawList()
    {
        $status = \YunShop::request()->status;
        $type = \YunShop::request()->withdrawal_type;

        $date = $this->timeData();

        switch ($type){
            case 'store' :
                if (app('plugins')->isEnabled('store-cashier')) {
                    return $this->storeData($date,$status);
                }
            break;
            case 'store_cashier' :
                if (app('plugins')->isEnabled('store-cashier')) {

                    return $this->storeCashier($date,$status);
                }
            break;
            case 'hotel' :
                if (app('plugins')->isEnabled('hotel')) {
                    return $this->hotel($date,$status);
                }
            break;
            case 'hotel_cashier' :
                if (app('plugins')->isEnabled('hotel')) {
                    return $this->hotelashier($date,$status);
                }
            break;
        }

    }

    /**
     * 获取月周，昨天，今天的起始时间戳
     */
    public function timeData()
    {
        $date = [];
        //今日起始时间
        $date['begin_today'] = mktime(0,0,0,date('m'),date('d'),date('Y'));
        $date['end_today'] = mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

        //获取一周的时间
        $date['begin_lastweek'] = mktime(0,0,0,date('m'),date('d')-date('w')+1,date('Y'));
        $date['end_lastweek'] = mktime(23,59,59,date('m'),date('d')-date('w')+7,date('Y'));

        //获取昨天的时间
        $date['begin_yesterday'] = mktime(0,0,0,date('m'),date('d')-1,date('Y'));
        $date['end_yesterday'] = mktime(0,0,0,date('m'),date('d'),date('Y'))-1;

        //获取当前月起始时间
        $date['begin_thismonth'] = mktime(0,0,0,date('m'),1,date('Y'));
        $date['end_thismonth'] = mktime(23,59,59,date('m'),date('t'),date('Y'));
        return $date;
    }

    /**
     * 门店数据
     */
    public function storeData($date,$status)
    {
        $stro = new StoreOrder();
        $store = Store::where('uid',\YunShop::app()->getMemberId())->first();
        $store_order = StoreOrder::where(['store_id'=>$store['id']]);
        $store_search['store_id'] = $store['id'];
        if (!empty($status) && $status != 3 || $status == 0){
            $store_order->where('has_settlement',$status);
        }
        $data = $store_order->select(['yz_plugin_store_order.id','yz_plugin_store_order.order_id','yz_plugin_store_order.store_id','yz_plugin_store_order.has_settlement','yz_plugin_store_order.amount','yz_plugin_store_order.created_at','yz_order.order_sn'])->join('yz_order','yz_plugin_store_order.order_id','yz_order.id')->where('plugin_id',32)->orderBy('yz_plugin_store_order.created_at', 'desc')->paginate(15)->toArray();
        foreach ($data['data'] as $key => $item){
            $datas[$key]['order_sn'] = $item['order_sn'];
            $datas[$key]['amount'] = $item['amount'];
            $datas[$key]['status'] = $item['has_settlement'];
            switch ($item['has_settlement']){
                case 0 : $datas[$key]['settlement'] = '未结算'; break;
                case 1 : $datas[$key]['settlement'] = '已结算'; break;
                case -1 : $datas[$key]['settlement'] = '已失效'; break;
            }
        }
        $data['data'] = $datas ?: [];
        //获取一月的提成
        $data['thismonth'] = $stro->join('yz_order','yz_plugin_store_order.order_id','yz_order.id')->where('yz_order.plugin_id',32)->where(['store_id'=>$store['id']])->whereBetween('yz_plugin_store_order.created_at',[ $date['begin_thismonth'] , $date['end_thismonth'] ])->sum('yz_plugin_store_order.amount');
        //获取一周的提成
        $data['lastweek'] = $stro->join('yz_order','yz_plugin_store_order.order_id','yz_order.id')->where('yz_order.plugin_id',32)->where(['store_id'=>$store['id']])->whereBetween('yz_plugin_store_order.created_at',[ $date['begin_lastweek'] , $date['end_lastweek'] ])->sum('yz_plugin_store_order.amount');
        //获取昨天的提成
        $data['yesterday'] = $stro->join('yz_order','yz_plugin_store_order.order_id','yz_order.id')->where('yz_order.plugin_id',32)->where(['store_id'=>$store['id']])->whereBetween('yz_plugin_store_order.created_at',[ $date['begin_yesterday'] , $date['end_yesterday'] ])->sum('yz_plugin_store_order.amount');
        //获取今天的提成
        $data['today'] = $stro->join('yz_order','yz_plugin_store_order.order_id','yz_order.id')->where('yz_order.plugin_id',32)->where(['store_id'=>$store['id']])->whereBetween('yz_plugin_store_order.created_at',[ $date['begin_today'] , $date['end_today'] ])->sum('yz_plugin_store_order.amount');
        if ($data['data']){
            return $this->successJson('查询成功',$data);
        }
        return $this->errorJson('查询失败',$data);
    }


    /**
     * 收银台数据
     */
    public function storeCashier($date,$status)
    {

        $data = [];
        $store = Store::where('uid',\YunShop::app()->getMemberId())->first();

        // 找到店主
        $cashier_order = new \Yunshop\StoreCashier\common\models\CashierOrder();
        $cashier_order_builder = $cashier_order->select(['yz_plugin_cashier_order.id','yz_plugin_cashier_order.order_id','yz_plugin_cashier_order.cashier_id','yz_plugin_cashier_order.has_settlement','yz_plugin_cashier_order.amount','yz_plugin_cashier_order.created_at','yz_order.order_sn'])->where('cashier_id',$store['cashier_id']);

        if (!empty($status) && $status != 3 || $status == 0){
            $cashier_order_builder->where('has_settlement',$status);
        }

        $data = $cashier_order_builder->join('yz_order','yz_order.id','yz_plugin_cashier_order.order_id')->orderBy('yz_plugin_cashier_order.created_at', 'desc')->paginate(15)->toArray();

        foreach ($data['data'] as $key => $item){
            $datas[$key]['order_sn'] = $item['order_sn'];
            $datas[$key]['created_at'] = $item['created_at'];
            $datas[$key]['status'] = $item['has_settlement'];
            $datas[$key]['amount'] = $item['amount'];
            switch ($item['has_settlement']){
                case 0 : $datas[$key]['settlement'] = '未结算'; break;
                case 1 : $datas[$key]['settlement'] = '已结算'; break;
                case -1 : $datas[$key]['settlement'] = '已失效'; break;
            }
        }

        $data['data'] = $datas ?: [];

        //获取一月的提成
        $data['thismonth'] = $cashier_order->where('cashier_id',$store['cashier_id'])->whereBetween('yz_plugin_cashier_order.created_at',[ $date['begin_thismonth'] , $date['end_thismonth'] ])->sum('yz_plugin_cashier_order.amount');
        //获取一周的提成
        $data['lastweek'] = $cashier_order->where('cashier_id',$store['cashier_id'])->whereBetween('yz_plugin_cashier_order.created_at',[ $date['begin_lastweek'] , $date['end_lastweek'] ])->sum('yz_plugin_cashier_order.amount');
        //获取昨天的提成
        $data['yesterday'] = $cashier_order->where('cashier_id',$store['cashier_id'])->whereBetween('yz_plugin_cashier_order.created_at',[ $date['begin_yesterday'] , $date['end_yesterday'] ])->sum('yz_plugin_cashier_order.amount');
        //获取今天的提成
        $data['today'] = $cashier_order->where('cashier_id',$store['cashier_id'])->whereBetween('yz_plugin_cashier_order.created_at',[ $date['begin_today'] , $date['end_today'] ])->sum('yz_plugin_cashier_order.amount');

        if ($data['data']){
            return $this->successJson('查询成功',$data);
        }
        return $this->successJson('查询失败',$data);
    }


    /**
     * j酒店数据
     */
    public function hotel($date,$status)
    {
        $data = [];
        $hotel = Hotel::where('uid',\YunShop::app()->getMemberId())->first();
        $hotel_order = HotelOrder::with('hasOneOrder')->where('hotel_id',$hotel['id']);

        if (!empty($status) && $status != 3 || $status == 0){
            $hotel_order->where('has_settlement',$status);
        }

        $data = $hotel_order->orderBy('created_at', 'desc')->paginate(15)
            ->toArray();
        $pager  = PaginationHelper::show($data['total'], $data['currentPage'], $data['perPage']);
        foreach ($data['data'] as $key => $itme){
//            dd($itme);
            $datas[$key]['order_sn'] = $itme['has_one_order']['order_sn'];
            $datas[$key]['created_at'] = $itme['created_at'];
            $datas[$key]['amount'] = $itme['amount'];
            $datas[$key]['status'] = $itme['has_settlement'];
            switch ($itme['has_settlement']){
                case 0 : $datas[$key]['settlement'] = '未结算'; break;
                case 1 : $datas[$key]['settlement'] = '已结算'; break;
                case -1 : $datas[$key]['settlement'] = '已失效'; break;
            }
        }
        $data['data'] = $datas ?: [];
        //获取一月的提成
        $data['thismonth'] = $hotel_order->whereBetween('created_at',[ $date['begin_thismonth'] , $date['end_thismonth'] ])->sum('amount');
        //获取一周的提成
        $data['lastweek'] = $hotel_order->whereBetween('created_at',[ $date['begin_lastweek'] , $date['end_lastweek'] ])->sum('amount');
        //获取昨天的提成
        $data['yesterday'] = $hotel_order->whereBetween('created_at',[ $date['begin_yesterday'] , $date['end_yesterday'] ])->sum('amount');
        //获取今天的提成
        $data['today'] = $hotel_order->whereBetween('created_at',[ $date['begin_today'] , $date['end_today'] ])->sum('amount');

        if ($data['data']){
            return $this->successJson('查询成功',$data);
        }
        return $this->successJson('查询失败',$data);
    }


    /**
     * 酒店收银台数据
     */
    public function hotelashier($date,$status)
    {
        $data = [];
        $hotel = Hotel::where('uid',\YunShop::app()->getMemberId())->first();
        $hotel_order = CashierOrder::with('hasOneOrder')->where('cashier_id',$hotel['cashier_id']);

        if (!empty($status) && $status != 3 || $status == 0){
            $hotel_order->where('has_settlement',$status);
        }

        $data = $hotel_order->orderBy('created_at', 'desc')->paginate(15)
            ->toArray();
        $pager  = PaginationHelper::show($data['total'], $data['currentPage'], $data['perPage']);
        foreach ($data['data'] as $key => $itme){
//            dd($itme);
            $datas[$key]['order_sn'] = $itme['has_one_order']['order_sn'];
            $datas[$key]['created_at'] = $itme['created_at'];
            $datas[$key]['amount'] = $itme['amount'];
            $datas[$key]['status'] = $itme['has_settlement'];
            switch ($itme['has_settlement']){
                case 0 : $datas[$key]['settlement'] = '未结算'; break;
                case 1 : $datas[$key]['settlement'] = '已结算'; break;
                case -1 : $datas[$key]['settlement'] = '已失效'; break;
            }
        }
        $data['data'] = $datas ?: [];
        //获取一月的提成
        $data['thismonth'] = $hotel_order->whereBetween('created_at',[ $date['begin_thismonth'] , $date['end_thismonth'] ])->sum('amount');
        //获取一周的提成
        $data['lastweek'] = $hotel_order->whereBetween('created_at',[ $date['begin_lastweek'] , $date['end_lastweek'] ])->sum('amount');
        //获取昨天的提成
        $data['yesterday'] = $hotel_order->whereBetween('created_at',[ $date['begin_yesterday'] , $date['end_yesterday'] ])->sum('amount');
        //获取今天的提成
        $data['today'] = $hotel_order->whereBetween('created_at',[ $date['begin_today'] , $date['end_today'] ])->sum('amount');

        if ($data['data']){
            return $this->successJson('查询成功',$data);
        }
        return $this->successJson('查询失败',$data);
    }
}