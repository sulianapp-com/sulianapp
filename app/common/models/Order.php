<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 上午11:32
 */

namespace app\common\models;


use app\backend\modules\order\observers\OrderObserver;
use app\common\events\order\AfterOrderCreatedImmediatelyEvent;
use app\common\events\order\AfterOrderPaidEvent;
use app\common\events\order\AfterOrderPaidImmediatelyEvent;
use app\common\events\order\AfterOrderReceivedEvent;
use app\common\events\order\AfterOrderReceivedImmediatelyEvent;
use app\common\events\order\AfterOrderSentImmediatelyEvent;
use app\common\exceptions\AppException;
use app\common\models\order\Express;
use app\common\models\order\OrderChangePriceLog;
use app\common\models\order\OrderCoinExchange;
use app\common\models\order\OrderCoupon;
use app\common\models\order\OrderDeduction;
use app\common\models\order\OrderDiscount;
use app\common\models\order\OrderFee;
use app\common\models\order\OrderSetting;
use app\common\models\order\Plugin;
use app\common\models\order\Remark;
use app\common\models\refund\RefundApply;
use app\common\modules\order\OrderOperationsCollector;
use app\common\modules\payType\events\AfterOrderPayTypeChangedEvent;
use app\common\modules\refund\services\RefundService;
use app\common\services\PayFactory;
use app\common\traits\HasProcessTrait;
use app\frontend\modules\order\OrderCollection;
use app\frontend\modules\order\services\OrderService;
use app\frontend\modules\order\services\status\StatusFactory;
use app\frontend\modules\orderPay\models\PreOrderPay;
use app\Jobs\OrderCreatedEventQueueJob;
use app\Jobs\OrderPaidEventQueueJob;
use app\Jobs\OrderReceivedEventQueueJob;
use app\Jobs\OrderSentEventQueueJob;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\DB;
use Yunshop\JdSupply\models\JdSupplyOrderGoods;
use Yunshop\StoreCashier\common\models\StoreOrder;

/**
 * Class Order
 * @package app\common\models
 * @property int uniacid
 * @property int id
 * @property int uid
 * @property string order_sn
 * @property int price
 * @property string statusName
 * @property string statusCode
 * @property int status
 * @property int pay_type_name
 * @property int pay_type_id
 * @property int order_pay_id
 * @property int is_pending
 * @property int is_virtual
 * @property int dispatch_type_id
 * @property int refund_id
 * @property int no_refund
 * @property float deduction_price
 * @property float order_goods_price
 * @property float discount_price
 * @property float dispatch_price
 * @property float change_price
 * @property float cost_amount
 * @property float change_dispatch_price
 * @property float fee_amount
 * @property int plugin_id
 * @property int is_plugin
 * @property Collection orderGoods
 * @property Collection allStatus
 * @property Collection orderCoinExchanges
 * @property Member belongsToMember
 * @property OrderDiscount discount
 * @property Collection orderPays
 * @property OrderPay hasOneOrderPay
 * @property PayType hasOnePayType
 * @property RefundApply hasOneRefundApply
 * @property Carbon finish_time
 * @property OrderCreatedJob orderCreatedJob
 * @property OrderPaidJob orderPaidJob
 * @property OrderReceivedJob orderReceivedJob
 * @property OrderSentJob orderSentJob
 * @property Address address
 * @property Address orderAddress
 * @property Express express
 * @method static self isPlugin()
 * @method static self orders(array $searchParam)
 * @method static self cancelled()
 */
class Order extends BaseModel
{
    use HasProcessTrait, DispatchesJobs;

    public $table = 'yz_order';
    public $setting = null;
    private $StatusService;
    protected $guarded = ['id'];
    protected $appends = ['status_name', 'pay_type_name'];
    protected $with = ['process','hasOnePayType'];
    protected $search_fields = ['yz_order.id', 'yz_order.order_sn'];
    protected $attributes = [
        'plugin_id' => 0,
        'is_virtual' => 0,
    ];
    static protected $needLog = true;
    //protected $attributes = ['discount_price'=>0];
    const CLOSE = -1;
    const WAIT_PAY = 0;
    const WAIT_SEND = 1;
    const WAIT_RECEIVE = 2;
    const COMPLETE = 3;
    const REFUND = 11;

    /**
     * 时间类型字段
     * @return array
     */
    public function getDates()
    {
        return ['create_time', 'refund_time', 'operate_time', 'send_time', 'return_time', 'end_time', 'pay_time', 'send_time', 'cancel_time', 'create_time', 'cancel_pay_time', 'cancel_send_time', 'finish_time'] + parent::getDates();
    }


    /**
     * 获取用户消费次数
     *
     * @param $uid
     * @return mixed
     */
    public static function getCostTotalNum($uid)
    {
        return self::where('status', '>=', 1)
            ->Where('status', '<=', 3)
            ->where('uid', $uid)
            ->count('id');
    }

    /**
     * 隐藏插件订单
     * 订单流程和标准订单不一样的插件订单，不显示在前端我的订单里
     * @param $query
     * @return mixed
     */

    public function scopeHidePluginIds($query, $plugin_ids = [])
    {
        if (empty($plugin_ids)) {
            //酒店订单、租赁订单、网约车订单、服务站补货订单、拼团订单
            $plugin_ids = [33, 40, 41, 43, 54];
        }

        return $query->whereNotIn('plugin_id', $plugin_ids);
    }


    /**
     * 获取用户消费总额
     *
     * @param $uid
     * @return mixed
     */
    public static function getCostTotalPrice($uid)
    {
        return self::where('status', '>=', 1)
            ->where('status', '<=', 3)
            ->where('uid', $uid)
            ->sum('price');
    }

    //获取发票信息
    public static function getInvoice($order)
    {
        //return self ::select('invoice_type','rise_type','call','company_number','invoice')
        return self::select('invoice_type', 'rise_type', 'collect_name', 'company_number', 'invoice')
            ->where('id', $order)
            ->first();
    }

    public function scopePayFail($query)
    {
        return $query->where('refund_id', '0');
    }

    /**
     * 订单状态:待付款
     * @param $query
     * @return mixed
     */
    public function scopeWaitPay($query)
    {
        //AND o.status = 0 and o.paytype<>3
        return $query->where([$this->getTable() . '.status' => self::WAIT_PAY]);
    }

    public function scopeNormal($query)
    {
        return $query->where('refund_id', 0)->where('is_pending', 0);
    }

    /**
     * 订单状态:待发货
     * @param $query
     * @return mixed
     */
    public function scopeWaitSend($query)
    {
        //AND ( o.status = 1 or (o.status=0 and o.paytype=3) )
        return $query->where([$this->getTable() . '.status' => self::WAIT_SEND]);
    }

    /**
     * 订单状态:待收货
     * @param $query
     * @return mixed
     */
    public function scopeWaitReceive($query)
    {
        return $query->where([$this->getTable() . '.status' => self::WAIT_RECEIVE]);
    }

    /**
     * 订单状态:完成
     * @param $query
     * @return mixed
     */
    public function scopeCompleted($query)
    {
        return $query->where([$this->getTable() . '.status' => self::COMPLETE]);
    }

    /**
     * 订单状态:退款中
     * @param $query
     * @return mixed
     */
    public function scopeRefund($query)
    {
        return $query->where('refund_id', '>', '0')->whereHas('hasOneRefundApply', function ($query) {
            return $query->refunding();
        });

    }

    /**
     * 订单状态:已退款
     * @param $query
     * @return mixed
     */
    public function scopeRefunded($query)
    {
        return $query->where('refund_id', '>', '0')->whereHas('hasOneRefundApply', function ($query) {
            return $query->refunded();
        });
    }

    /**
     * 订单状态:取消
     * @param $query
     * @return mixed
     */
    public function scopeCancelled($query)
    {
        return $query->where(['status' => self::CLOSE]);
    }

    /**
     * 关联模型 1对多:订单商品
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @throws \app\common\exceptions\ShopException
     */
    public function hasManyOrderGoods()
    {
        return $this->hasMany(self::getNearestModel('OrderGoods'), 'order_id', 'id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @throws \app\common\exceptions\ShopException
     */
    public function orderGoods()
    {
        return $this->hasMany(self::getNearestModel('OrderGoods'), 'order_id', 'id');
    }

    /**
     * 关联模型 1对多:订单优惠信息
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function discounts()
    {
        return $this->hasMany(app('OrderManager')->make('OrderDiscount'), 'order_id', 'id');
    }

    /**
     * 关联模型 1对多:订单抵扣信息
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deductions()
    {
        return $this->hasMany(app('OrderManager')->make('OrderDeduction'), 'order_id', 'id');
    }

    /**
     * 关联模型 1对多:订单信息
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function coupons()
    {
        return $this->hasMany(app('OrderManager')->make('OrderCoupon'), 'order_id', 'id');
    }

    public function orderCoupons()
    {
        return $this->hasMany(app('OrderManager')->make('OrderCoupon'), 'order_id', 'id');
    }

    /**
     * 关联模型 1对多:改价记录
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderChangePriceLogs()
    {
        return $this->hasMany(OrderChangePriceLog::class, 'order_id', 'id');
    }

    public function hasOneStoreOrder()
    {
        return $this->hasOne(StoreOrder::class, 'order_id', 'id');
    }

    public function hasManyJdOrderGoods()
    {
        return $this->hasMany(JdSupplyOrderGoods::Class, 'order_id', 'id');
    }

    /**
     * 关联模型 1对1:购买者
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function belongsToMember()
    {
        return $this->belongsTo(Member::class, 'uid', 'uid');
    }

    /**
     * 关联模型 1对1:退款列表
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneRefundApply()
    {
        return $this->hasOne(RefundApply::class, 'id', 'refund_id')->orderBy('created_at', 'desc');

    }

    /**
     * 关联模型 1对1:订单配送方式
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneDispatchType()
    {
        return $this->hasOne(DispatchType::class, 'id', 'dispatch_type_id');
    }

    /**
     * 关联模型 1对1:订单备注
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneOrderRemark()
    {
        return $this->hasOne(Remark::class, 'order_id', 'id');
    }

    /**
     * 关联模型 1对1:支付方式
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOnePayType()
    {
        return $this->hasOne(PayType::class, 'id', 'pay_type_id');
    }

    /**
     * 关联模型 1对1:订单支付信息
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hasOneOrderPay()
    {
        return $this->belongsTo(OrderPay::class, 'order_pay_id', 'id');
    }

    /**
     * 关联模型 1对1:订单快递
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function express()
    {
        return $this->hasOne(Express::class, 'order_id', 'id');
    }

    /**
     * 对应每个订单状态的状态类,过于啰嗦,考虑删除
     * @return \app\frontend\modules\order\services\status\Complete|\app\frontend\modules\order\services\status\WaitPay|\app\frontend\modules\order\services\status\WaitReceive|\app\frontend\modules\order\services\status\WaitSend
     */
    public function getStatusService()
    {
        if (!isset($this->StatusService)) {
            $this->StatusService = (new StatusFactory($this))->create();
        }
        return $this->StatusService;
    }

    /**
     * 关联模型 1对1:收货地址
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function address()
    {
        return $this->hasOne(OrderAddress::class, 'order_id', 'id');
    }

    public function orderAddress()
    {
        return $this->hasOne(OrderAddress::class, 'order_id', 'id');
    }

    /**
     * 关联模型 1对1:订单支付信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOnePay()
    {
        return $this->hasOne(OrderPay::class, 'order_id', 'id');
    }

    public function getStatusCodeAttribute()
    {
        return app('OrderManager')->setting('status')[$this->status];
    }

    /**
     * @return array
     */
    public function getOperationsSetting()
    {
        return [];
    }

    public function isClose()
    {
        return $this->status == self::CLOSE;
    }

    /**
     * 订单状态汉字
     * @return string
     */
    public function getStatusNameAttribute()
    {
        if (!$this->isClose() && $this->currentProcess()) {
            return $this->currentProcess()->status_name;
        }
        $statusName = $this->getStatusService()->getStatusName();
        if ($this->isPending()) {
            $statusName .= ' : 锁定';
        }

        return $statusName;
    }

    /**
     * 支付类型汉字
     * @return string
     */
    public function getPayTypeNameAttribute()
    {

        if ($this->pay_type_id != PayType::CASH_PAY && $this->status == self::WAIT_PAY) {
            return '未支付';
        }
        if ($this->pay_type_id == 3) {
            $set = \Setting::get('shop.shop');
            return $set['credit'] ?: '余额';
        }
        return $this->hasOnePayType->name;
    }

    /**
     * 订单可点的按钮
     * @return array
     */
    public function getButtonModelsAttribute()
    {
        $result = $this->memberButtons();

        return $result;
    }

    private function memberButtons()
    {
        return app('OrderManager')->make(OrderOperationsCollector::class)->getOperations($this);
    }

    /**
     * 按状态分组获取订单数量
     * @param $query
     * @param array $status
     * @return array
     */
    public function scopeGetOrderCountGroupByStatus($query, $status = [])
    {
        //$status = [Order::WAIT_PAY, Order::WAIT_SEND, Order::WAIT_RECEIVE, Order::COMPLETE, Order::REFUND];
        $status_counts = $query->select('status', DB::raw('count(*) as total'))
            ->whereIn('status', $status)->where('plugin_id', '<', 900)
            ->HidePluginIds()
            ->groupBy('status')->get()->makeHidden(['status_name', 'pay_type_name', 'has_one_pay_type', 'button_models'])
            ->toArray();
        if (in_array(Order::REFUND, $status)) {
            $refund_count = $query->refund()->count();
            $status_counts[] = ['status' => Order::REFUND, 'total' => $refund_count];
        }
        foreach ($status as $state) {
            if (!in_array($state, array_column($status_counts, 'status'))) {
                $status_counts[] = ['status' => $state, 'total' => 0];
            }
        }
        return $status_counts;
    }

    /**
     * 区分订单属于插件或商城,考虑使用新添加的scopePluginId方法替代
     * @param $query
     * @return mixed
     */
    public function scopeIsPlugin($query)
    {
        return $query->where('is_plugin', 0);
    }

    /**
     * 用来区分订单属于哪个.当插件需要查询自己的订单时,复写此方法
     * @param $query
     * @param int $pluginId
     * @return mixed
     */
    public function scopePluginId($query, $pluginId = 0)
    {
        return $query->where('plugin_id', $pluginId);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderPlugin()
    {
        return $this->hasMany(Plugin::class);
    }

    /**
     * 用来区分订单属于哪个.当插件需要查询自己的订单时,复写此方法
     * @param $query
     * @param int $pluginId
     * @return mixed
     */
    public function scopeHasPluginId($query, $pluginId = 0)
    {
        if (!$pluginId) {
            return $query;
        }

        return $query->whereHas('orderPlugin', function ($query) use ($pluginId) {
            $query->where('plugin_id', $pluginId);
        });
    }

    /**
     * 通过会员ID获取订单信息
     * @param $member_id
     * @param $status
     * @return mixed
     */
    public static function getOrderInfoByMemberId($member_id, $status)
    {
        return self::where('uid', $member_id)->isComment($status);
    }

    /**
     * 关系链 指定商品
     *
     * @param $uid
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getOrderListByUid($uid)
    {
        return self::select(['*'])
            ->where('uid', $uid)
            ->where('status', '>=', 1)
            ->where('status', '<=', 3)
            ->with(['hasManyOrderGoods' => function ($query) {
                return $query->select(['*']);
            }])
            ->get();
    }

    public function isVirtual()
    {
        return $this->is_virtual == 1;
    }

    public function orderDeduction()
    {
        return $this->hasMany(OrderDeduction::class, 'order_id', 'id');
    }

    public function orderDeductions()
    {
        return $this->hasMany(OrderDeduction::class, 'order_id', 'id');
    }

    public function orderCoupon()
    {
        return $this->hasMany(OrderCoupon::class, 'order_id', 'id');
    }

    public function orderDiscounts()
    {
        return $this->hasMany(OrderDiscount::class, 'order_id', 'id');
    }

    public function orderFees()
    {
        return $this->hasMany(OrderFee::class, 'order_id', 'id');
    }

    public function orderDiscount()
    {
        return $this->hasMany(OrderDiscount::class, 'order_id', 'id');
    }

    public function receive()
    {
        return \app\frontend\modules\order\services\OrderService::orderReceive(['order_id' => $this->id]);
    }

    public function orderPays()
    {
        return $this->belongsToMany(OrderPay::class, (new OrderPayOrder())->getTable(), 'order_id', 'order_pay_id');
    }

    public function close()
    {
        return \app\backend\modules\order\services\OrderService::close($this);
    }

    /**
     * 初始化方法
     */
    public static function boot()
    {
        parent::boot();
        static::observe(new OrderObserver());
        // 添加了公众号id的全局条件.
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
            $builder->hasPluginId();
        });
    }

    public function needSend()
    {
        return isset($this->hasOneDispatchType) && $this->hasOneDispatchType->needSend();
    }

    public function orderSettings()
    {
        return $this->hasMany(OrderSetting::class, 'order_id', 'id');
    }

    public function setPayTypeIdAttribute($value)
    {
        $this->attributes['pay_type_id'] = $value;
        if ($this->pay_type_id != $this->getOriginal('pay_type_id')) {
            event(new AfterOrderPayTypeChangedEvent($this));
        }
    }

    /**
     * @param $value
     * @throws AppException
     */
    public function setStatusAttribute($value)
    {
        if ($this->isPending()) {
            throw new AppException("订单已锁定,无法继续操作");
        }
        $this->attributes['status'] = $value;
    }

    public function isPending()
    {
        return $this->is_pending;
    }

    public function getSetting($key)
    {
        // 全局设置
        $result = \app\common\facades\Setting::get($key);

        if (isset($this->orderSettings) && $this->orderSettings->isNotEmpty()) {
            // 订单设置
            $keys = collect(explode('.', $key));
            $orderSettingKey = $keys->shift();
            if ($orderSettingKey == 'plugin') {
                // 获取第一个不为plugin的key
                $orderSettingKey = $keys->shift();
            }
            $orderSettingValueKeys = $keys;
            if ($orderSettingValueKeys->isNotEmpty()) {


                $orderSettingValue = array_get($this->orderSettings->where('key', $orderSettingKey)->first()->value, $orderSettingValueKeys->implode('.'));

            } else {
                $orderSettingValue = $this->orderSettings->where('key', $orderSettingKey)->first()->value;
            }

            if (isset($orderSettingValue)) {
                if (is_array($result)) {
                    // 数组合并
                    $result = array_merge($result, $orderSettingValue);
                } else {
                    // 其他覆盖
                    $result = $orderSettingValue;
                }
            }
        }

        return $result;
    }

    //关联商城订单表
    public function hasOneMemberShopInfo()
    {
        return $this->hasOne(MemberShopInfo::class, 'member_id', 'uid');

    }

    /**
     * 已退款
     * @return bool
     */
    public function isRefunded()
    {
        // 存在处理中的退款申请
        if (empty($this->refund_id) || !isset($this->hasOneRefundApply)) {
            return false;
        }
        if ($this->hasOneRefundApply->isRefunded()) {
            return true;
        }
        return false;
    }

    /**
     * 退款中
     * @return bool
     */
    public function isRefunding()
    {
        // 存在处理中的退款申请
        if (empty($this->refund_id) || !isset($this->hasOneRefundApply)) {
            return false;
        }
        if ($this->hasOneRefundApply->isRefunding()) {
            return true;
        }
        return false;
    }

    /**
     * 可以退款
     * @return bool
     */
    public function canRefund()
    {
        //关闭后不许退款
        if (!RefundService::allowRefund()) {
            return false;
        }

        if ($this->status == self::COMPLETE) {
            // 完成后n天不许退款
            if ($this->finish_time->diffInDays() > \Setting::get('shop.trade.refund_days')) {
                return false;
            }

            // 完成后不许退款
            if (\Setting::get('shop.trade.refund_days') === '0') {
                return false;
            }

        }

        // 存在处理中的退款申请
        if (!empty($this->refund_id) || isset($this->hasOneRefundApply)) {
            return false;
        }
        return true;
    }

    public function getAllStatusAttribute()
    {
        return collect([
            [
                'id' => self::CLOSE,
                'name' => '已关闭',
            ], [
                'id' => self::WAIT_PAY,
                'name' => '待支付',
            ], [
                'id' => self::WAIT_SEND,
                'name' => '待发货',
            ], [
                'id' => self::WAIT_RECEIVE,
                'name' => '待收货',
            ], [
                'id' => self::COMPLETE,
                'name' => '已完成',
            ], [
                'id' => self::REFUND,
                'name' => '已退款',
            ],

        ]);
    }

    /**
     * 后台支付
     * @throws AppException
     */
    public function backendPay()
    {
        // 生成支付记录 记录订单号,支付金额,用户,支付号
        $orderPay = new PreOrderPay(['pay_type_id' => PayType::BACKEND]);
        // 添加关联订单
        $orders = new OrderCollection([$this]);
        $orderPay->setOrders($orders);
        $orderPay->store();
        // 获取支付信息
        $orderPay->getPayResult(PayFactory::PAY_BACKEND);
        // 保存支付状态
        $orderPay->pay();
    }

    /**
     * 系统退款
     * @throws AppException
     */
    public function refund()
    {
        $result = $this->hasOneOrderPay->fastRefund($this);

        if (isset($result['batch_no'])) { //兼容支付宝老接口
            $refundApply = new RefundApply(['reason' => '后台退款', 'content' => '后台退款', 'refund_type' => 1, 'order_id' => $this->id]);
            $refundApply->images = '';
            $refundApply->refund_sn = \app\frontend\modules\refund\services\RefundService::createOrderRN();
            $refundApply->create_time = time();
            $refundApply->price = $this->price;
            $refundApply->alipay_batch_sn = $result['batch_no'];
            $refundApply->uid = 0;
            $refundApply->uniacid = $this->uniacid;
            $refundApply->status = 0;

            if (!$refundApply->save()) {
                throw  new AppException('后台申请退款失败');
            }
        } else {
            OrderService::orderForceClose(['order_id' => $this->id]);
        }

        return $result;
    }

    public function fireCreatedEvent()
    {
        event(new AfterOrderCreatedImmediatelyEvent($this));

        if ($this->uid == 0) {
            return;
        }
        $this->dispatch(new OrderCreatedEventQueueJob($this->id));
        OrderCreatedJob::create([
            'order_id' => $this->id,
        ]);
    }

    public function firePaidEvent()
    {
        event(new AfterOrderPaidImmediatelyEvent($this));

        if ($this->uid == 0) {
            return;
        }
        // 去掉同步设置（已没用，相关设置也注释掉了，之前为了解决成为分销商和分销升级异步问题）
//        if (\Setting::get('shop.order.paid_process')) {
//            //同步
//            event(new AfterOrderPaidEvent($this));
//
//        } else {
        //异步
        $this->dispatch(new OrderPaidEventQueueJob($this->id));
        OrderPaidJob::create([
            'order_id' => $this->id,
        ]);
//        }
    }

    public function fireSentEvent()
    {
        event(new AfterOrderSentImmediatelyEvent($this));
        if ($this->uid == 0) {
            return;
        }
        //异步
        $this->dispatch(new OrderSentEventQueueJob($this->id));
        OrderSentJob::create([
            'order_id' => $this->id,
        ]);
    }

    public function fireReceivedEvent()
    {
        event(new AfterOrderReceivedImmediatelyEvent($this));
        if ($this->uid == 0) {
            return;
        }
        // 去掉同步设置（已没用，相关设置也注释掉了，之前为了解决成为分销商和分销升级异步问题）
//        if (\Setting::get('shop.order.receive_process')) {
//            //同步
//            event(new AfterOrderReceivedEvent($this));
//
//        } else {
        //异步
        $this->dispatch(new OrderReceivedEventQueueJob($this->id));
        OrderReceivedJob::create([
            'order_id' => $this->id,
        ]);
//        }
    }

    public function orderCreatedJob()
    {
        return $this->hasOne(OrderCreatedJob::class, 'order_id');
    }

    public function orderSentJob()
    {
        return $this->hasOne(OrderSentJob::class, 'order_id');
    }

    public function orderPaidJob()
    {
        return $this->hasOne(OrderPaidJob::class, 'order_id');
    }

    public function orderReceivedJob()
    {
        return $this->hasOne(OrderReceivedJob::class, 'order_id');
    }

    public function stockEnough()
    {
        $this->orderGoods->each(function (OrderGoods $orderGoods) {
            // 付款后扣库存
            if ($orderGoods->goods->reduce_stock_method == 1) {
                $orderGoods->stockEnough();
            }
        });
    }

    public function orderRequest()
    {
        return $this->hasOne(OrderRequest::class, 'order_id');
    }

    public function orderCoinExchanges()
    {
        return $this->hasMany(OrderCoinExchange::class, 'order_id');
    }
}