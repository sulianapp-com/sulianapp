<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/4
 * Time: 上午9:09
 */

namespace app\backend\modules\order\controllers;

use app\backend\modules\goods\models\GoodsOption;
use app\backend\modules\member\models\MemberParent;
use app\backend\modules\order\models\Order;
use app\backend\modules\order\models\OrderGoods;
use app\backend\modules\order\models\OrderJoinOrderGoods;
use app\common\components\BaseController;

use app\common\helpers\PaginationHelper;
use app\common\services\ExportService;
use app\common\services\member\level\LevelUpgradeService;
use Illuminate\Support\Facades\DB;
use Yunshop\TeamDividend\models\TeamDividendLevelModel;

class ListController extends BaseController
{
    /**
     * 页码
     */
    const PAGE_SIZE = 10;
    /**
     * @var Order
     */
    protected $orderModel;

    public function preAction()
    {
        parent::preAction();
        $params = \YunShop::request()->get();
        $this->orderModel = $this->getOrder()->orders($params['search']);
    }

    protected function getOrder()
    {
        return Order::isPlugin()->pluginId();
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {
        $this->export($this->orderModel);
        $this->directExport($this->orderModel);
        return view('order.index', $this->getData())->render();
    }



    /**
     * @return string
     * @throws \Throwable
     */
    public function waitPay()
    {
        $this->orderModel->waitPay();
        $this->export($this->orderModel->waitPay());
        $this->directExport($this->orderModel->waitPay());
        return view('order.index', $this->getData())->render();
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function waitSend()
    {
        // 会员排序
        $sort = request()->search['sort'];
        $condition = [];
        if ($sort == 1) {
            $condition['order_by'][] = [$this->orderModel->getModel()->getTable() . '.uid', 'desc'];
            $condition['order_by'][] = [$this->orderModel->getModel()->getTable() . '.id', 'desc'];
        }
        $this->orderModel->waitSend();
        $this->export($this->orderModel->waitSend());
        $this->directExport($this->orderModel->waitSend());
        return view('order.index', $this->getData($condition))->render();
        //return view('order.index', $this->getData())->render();
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function waitReceive()
    {
        $this->orderModel->waitReceive();
        $this->export($this->orderModel->waitReceive());
        $this->directExport($this->orderModel->waitReceive());
        return view('order.index', $this->getData())->render();
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function completed()
    {

        $this->orderModel->completed();
        $this->export($this->orderModel->completed());
        $this->directExport($this->orderModel->completed());
        return view('order.index', $this->getData())->render();
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function cancelled()
    {
        $this->orderModel->cancelled();
        $this->export($this->orderModel->cancelled());
        $this->directExport($this->orderModel->cancelled());
        return view('order.index', $this->getData())->render();
    }

    protected function getData($condition = [])
    {
        $sort = request()->search['sort'];
        if ($sort == 1 && (!$condition || !$condition['order_by'])) {
            $condition['order_by'][] = [$this->orderModel->getModel()->getTable() . '.id', 'desc'];
        }
        /*$params = [
            'search' => [
                'ambiguous' => [
                    'field' => 'order_goods',
                    'string' => '春',
                ],
                'pay_type' => 1,
                'time_range' => [
                    'field' => 'create_time',
                    'range' => [1458425047, 1498425047]
                ]
            ]
        ];*/
        $requestSearch = \YunShop::request()->get('search');
        $requestSearch['plugin'] = 'fund';
        if ($requestSearch) {
            $requestSearch = array_filter($requestSearch, function ($item) {
                return !empty($item);
            });
        }


        $list['total_price'] = $this->orderModel->sum('price');
        $build = $this->orderModel;
        if ($sort == 1) {
            foreach ($condition['order_by'] as $item) {
                $build->orderBy(...$item);
            }
        } else {
            $build->orderBy($this->orderModel->getModel()->getTable() . '.id', 'desc');
        }

        $page = $build->paginate(self::PAGE_SIZE);

        foreach ($page as $item){
            $item->canRefund = $item->canRefund();
        }
        $list += $page->toArray();


        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);

        $data = [
            'list' => $list,
            'total_price' => $list['total_price'],
            'pager' => $pager,
            'requestSearch' => $requestSearch,
            'var' => \YunShop::app()->get(),
            'url' => request('route'),
            'include_ops' => 'order.ops',
            'detail_url' => 'order.detail',
            'route' => request()->route
        ];
        return $data;
    }

    public function export($orders)
    {
        if (\YunShop::request()->export == 1) {
            $export_page = request()->export_page ? request()->export_page : 1;
            //清除之前没有导出的文件
            if ($export_page == 1){
                $fileNameArr = file_tree(storage_path('exports'));
                foreach ($fileNameArr as $val ) {
                    if(file_exists(storage_path('exports/' . basename($val)))){
                        unlink(storage_path('exports/') . basename($val)); // 路径+文件名称
                    }
                }
            }
            $orders = $orders->with(['discounts', 'deductions'])->orderBy($this->orderModel->getModel()->getTable() . '.id', 'desc');
            $export_model = new ExportService($orders, $export_page);
            if (!$export_model->builder_model->isEmpty()) {
                $file_name = date('Ymdhis', time()) . '订单导出';//返现记录导出
                $export_data[0] = $this->getColumns();
                foreach ($export_model->builder_model->toArray() as $key => $item) {
                    $address = explode(' ', $item['address']['address']);
                    $fistOrder = $item['has_many_first_order'] ? '首单' : '';

                    $export_data[$key + 1] = [
                        $item['id'],
                        $item['order_sn'],
                        $item['has_one_order_pay']['pay_sn'],
                        $item['belongs_to_member']['uid'],
                        $this->getNickname($item['belongs_to_member']['nickname']),
                        $item['address']['realname'],
                        $item['address']['mobile'],
                        !empty($address[0]) ? $address[0] : '',
                        !empty($address[1]) ? $address[1] : '',
                        !empty($address[2]) ? $address[2] : '',
                        $item['address']['address'],
                        $this->getGoods($item, 'goods_title'),
                        $this->getGoods($item, 'goods_sn'),
                        $this->getGoods($item, 'total'),
                        $item['pay_type_name'],
                        $this->getExportDiscount($item, 'deduction'),
                        $this->getExportDiscount($item, 'coupon'),
                        $this->getExportDiscount($item, 'enoughReduce'),
                        $this->getExportDiscount($item, 'singleEnoughReduce'),
                        $item['goods_price'],
                        $item['dispatch_price'],
                        $item['price'],
                        $this->getGoods($item, 'cost_price'),
                        $item['status_name'],
                        $item['create_time'],
                        !empty(strtotime($item['pay_time'])) ? $item['pay_time'] : '',
                        !empty(strtotime($item['send_time'])) ? $item['send_time'] : '',
                        !empty(strtotime($item['finish_time'])) ? $item['finish_time'] : '',
                        $item['express']['express_company_name'],
                        '[' . $item['express']['express_sn'] . ']',
                        $item['has_one_order_remark']['remark'],
                        $item['note'],
                        $fistOrder,
                        $item['belongs_to_member']['realname'],
                        ' '.$item['belongs_to_member']['idcard'],
                    ];
                }
                $export_model->export($file_name, $export_data, 'order.list.index');
            }
        }
    }

    public function directExport($orders)
    {
        if (\YunShop::request()->direct_export == 1) {
            $export_page = request()->export_page ? request()->export_page : 1;
            $orders = $orders->with([
                'discounts',
                'deductions',
                'hasManyParentTeam' => function($q) {
                    $q->whereHas('hasOneTeamDividend')
                        ->with(['hasOneTeamDividend' => function($q) {
                            $q->with(['hasOneLevel']);
                        }])
                        ->with('hasOneMember')
//                        ->orderBy('id', 'desc')
                        ->orderBy('level', 'asc');
                },
            ]);
            $export_model = new ExportService($orders, $export_page);
            $team_list = TeamDividendLevelModel::getList()->get();

            $levelId = [];
            $level_name = [];
            foreach ($team_list as $level) {
                $level_name[] = $level->level_name;
                $levelId[] = $level->id;
            }

            if (!$export_model->builder_model->isEmpty()) {
                $file_name = date('Ymdhis', time()) . '订单导出';//返现记录导出
                $export_data[0] = array_merge($level_name,$this->getColumns());
                foreach ($export_model->builder_model->toArray() as $key => $item) {

                    $level = $this->getLevel($item, $levelId);

                    $export_data[$key + 1] = $level;

                    $address = explode(' ', $item['address']['address']);

                    array_push($export_data[$key + 1],
                        $item['id'],
                        $item['order_sn'],
                        $item['has_one_order_pay']['pay_sn'],
                        $item['belongs_to_member']['uid'],
                        $this->getNickname($item['belongs_to_member']['nickname']),
                        $item['address']['realname'],
                        $item['address']['mobile'],
                        !empty($address[0]) ? $address[0] : '',
                        !empty($address[1]) ? $address[1] : '',
                        !empty($address[2]) ? $address[2] : '',
                        $item['address']['address'],
                        $this->getGoods($item, 'goods_title'),
                        $this->getGoods($item, 'goods_sn'),
                        $this->getGoods($item, 'total'),
                        $item['pay_type_name'],
                        $this->getExportDiscount($item, 'deduction'),
                        $this->getExportDiscount($item, 'coupon'),
                        $this->getExportDiscount($item, 'enoughReduce'),
                        $this->getExportDiscount($item, 'singleEnoughReduce'),
                        $item['goods_price'],
                        $item['dispatch_price'],
                        $item['price'],
                        $this->getGoods($item, 'cost_price'),
                        $item['status_name'],
                        $item['create_time'],
                        !empty(strtotime($item['pay_time'])) ? $item['pay_time'] : '',
                        !empty(strtotime($item['send_time'])) ? $item['send_time'] : '',
                        !empty(strtotime($item['finish_time'])) ? $item['finish_time'] : '',
                        $item['express']['express_company_name'],
                        '[' . $item['express']['express_sn'] . ']',
                        $item['has_one_order_remark']['remark'],
                        $item['note']
                        );
                }
                $export_model->export($file_name, $export_data, 'order.list.index', 'direct_export');
            }
        }
    }

    public function getLevel($member, $levelId)
    {
        $data = [];
        foreach ($levelId as $k => $value) {
            foreach ($member['has_many_parent_team'] as $key => $parent) {

                if ($parent['has_one_team_dividend']['has_one_level']['id'] == $value) {
                    $data[$k] = $parent['has_one_member']['nickname'].' '.$parent['has_one_member']['realname'].' '.$parent['has_one_member']['mobile'];
                    break;
                }
            }
            $data[$k] = $data[$k] ?: '';
        }

        return $data;
    }

    private function getColumns()
    {
        return ["订单id","订单编号", "支付单号", "会员ID", "粉丝昵称", "会员姓名", "联系电话", '省', '市', '区', "收货地址", "商品名称", "商品编码", "商品数量", "支付方式", '抵扣金额', '优惠券优惠', '全场满减优惠', '单品满减优惠', "商品小计", "运费", "应收款", "成本价", "状态", "下单时间", "付款时间", "发货时间", "完成时间", "快递公司", "快递单号", "订单备注", "用户备注", "首单" , "真实姓名" , "身份证"];
    }

    protected function getExportDiscount($order, $key)
    {
        $export_discount = [
            'deduction' => 0,    //抵扣金额
            'coupon' => 0,    //优惠券优惠
            'enoughReduce' => 0,  //全场满减优惠
            'singleEnoughReduce' => 0,    //单品满减优惠
        ];

        foreach ($order['discounts'] as $discount) {

            if ($discount['discount_code'] == $key) {

                $export_discount[$key] = $discount['amount'];
            }
        }
        
        if (!$export_discount['deduction']) {

            foreach ($order['deductions'] as $k => $v) {
                
                $export_discount['deduction'] += $v['amount'];
            }
        }

        return $export_discount[$key];
    }

    private function getGoods($order, $key)
    {
        $goods_title = '';
        $goods_sn = '';
        $total = '';
        $cost_price = 0;
        foreach ($order['has_many_order_goods'] as $goods) {
            $res_title = $goods['title'];
            $res_title = str_replace('-', '，', $res_title);
            $res_title = str_replace('+', '，', $res_title);
            $res_title = str_replace('/', '，', $res_title);
            $res_title = str_replace('*', '，', $res_title);
            $res_title = str_replace('=', '，', $res_title);

            if ($goods['goods_option_title']) {
                $res_title .= '[' . $goods['goods_option_title'] . ']';
            }
            $order_goods = OrderGoods::find($goods['id']);
            if ($order_goods->goods_option_id) {
                $goods_option = GoodsOption::find($order_goods->goods_option_id);
                if ($goods_option) {
                    $goods_sn .= '【' . $goods_option->goods_sn . '】';
                }
            } else {
                $goods_sn .= '【' . $goods['goods_sn'] . '】';
            }

            $goods_title .= '【' . $res_title . '*' . $goods['total'] . '】';
            $total .= '【' . $goods['total'] . '】';
            $cost_price += $goods['goods_cost_price'];
        }
        $res = [
            'goods_title' => $goods_title,
            'goods_sn' => $goods_sn,
            'total' => $total,
            'cost_price' => $cost_price
        ];
        return $res[$key];
    }

    private function getNickname($nickname)
    {
        if (substr($nickname, 0, strlen('=')) === '=') {
            $nickname = '，' . $nickname;
        }
        return $nickname;
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function callbackFail()
    {
        $orderIds = DB::table('yz_order as o')->join('yz_order_pay_order as opo', 'o.id', '=', 'opo.order_id')
            ->join('yz_order_pay as op', 'op.id', '=', 'opo.order_pay_id')
            ->join('yz_pay_order as po', 'po.out_order_no', '=', 'op.pay_sn')
            ->whereIn('o.status', [0, -1])
            ->where('op.status', 0)
            ->where('po.status', 2)
            ->distinct()->pluck('o.id');
        $this->orderModel = Order::orders(request('search'))->whereIn('id', $orderIds);
        return view('order.index', $this->getData())->render();

    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function payFail()
    {
        $orderIds = DB::table('yz_order as o')->join('yz_order_pay_order as opo', 'o.id', '=', 'opo.order_id')
            ->join('yz_order_pay as op', 'op.id', '=', 'opo.order_pay_id')
            ->whereIn('o.status', [0, -1])
            ->where('op.status', 1)
            ->pluck('o.id');
        $this->orderModel = Order::orders(request('search'))->whereIn('id', $orderIds);
        return view('order.index', $this->getData())->render();

    }
}