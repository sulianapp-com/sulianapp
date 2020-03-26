<?php

namespace app\common\modules\shop;

class ShopConfig
{
    /**
     * @var self
     */
    static $current;

    private $items;

    /**
     *  constructor.
     */
    public function __construct()
    {
        self::$current = $this;
    }

    static public function current()
    {
        if (!isset(self::$current)) {
            return new static();
        }
        return self::$current;
    }

    private function _getItems()
    {
        $result = [
            'plugin' => [
                [
                    'id' => 31,
                    'name' => 'cashier'
                ],
                [
                    'id' => 32,
                    'name' => 'store'
                ],
                [
                    'id' => 33,
                    'name' => 'hotel'
                ],
                [
                    'id' => 36,
                    'name' => 'hotel-cashier'
                ],
                [
                    'id' => 51,
                    'name' => 'provider-platform'
                ],
                [
                    'id' => 53,
                    'name' => 'tripartite-provider'
                ],
                [
                    'id' => 92,
                    'name' => 'supplier'
                ],
                [
                    'id' => 41,
                    'name' => 'net-car'
                ],
                [
                    'id' => 44,
                    'name' => 'jd-supply'
                ],

            ],
            'observer' => [
                'goods' => [
                    'sale' => [
                        'class' => 'app\backend\modules\goods\models\Sale',
                        'function_validator' => 'relationValidator',
                        'function_save' => 'relationSave'
                    ],
                    'notice' => [
                        'class' => 'app\backend\modules\goods\models\Notice',
                        'function_validator' => 'relationValidator',
                        'function_save' => 'relationSave'
                    ],
                    'share' => [
                        'class' => 'app\backend\modules\goods\models\Share',
                        'function_validator' => 'relationValidator',
                        'function_save' => 'relationSave'
                    ],
                    'privilege' => [
                        'class' => 'app\backend\modules\goods\models\Privilege',
                        'function_validator' => 'relationValidator',
                        'function_save' => 'relationSave'
                    ],
                    'discount' => [
                        'class' => 'app\backend\modules\goods\models\Discount',
                        'function_validator' => 'relationValidator',
                        'function_save' => 'relationSave'
                    ],
                    'dispatch' => [
                        'class' => 'app\backend\modules\goods\models\GoodsDispatch',
                        'function_validator' => 'relationValidator',
                        'function_save' => 'relationSave'
                    ],
                    'coupon' => [
                        'class' => 'app\backend\modules\goods\models\Coupon',
                        'function_validator' => 'relationValidator',
                        'function_save' => 'relationSave'
                    ],
                    'div_from' => [
                        'class' => 'app\backend\modules\goods\models\DivFrom',
                        'function_validator' => 'relationValidator',
                        'function_save' => 'relationSave'
                    ],
                    'filtering' => [
                        'class' => 'app\backend\modules\goods\models\GoodsFiltering',
                        'function_validator' => 'relationValidator',
                        'function_save' => 'relationSave'
                    ],
                    'limitbuy' => [
                        'class' => 'app\backend\modules\goods\models\LimitBuy',
                        'function_validator' => 'relationValidator',
                        'function_save' => 'relationSave'
                    ],
                    'invite_page' => [
                        'class' => 'app\backend\modules\goods\models\InvitePage',
                        'function_validator' => 'relationValidator',
                        'function_save' => 'relationSave'
                    ],
                    'service' => [
                        'class' => 'app\backend\modules\goods\models\GoodsService',
                        'function_validator' => 'relationValidator',
                        'function_save' => 'relationSave'
                    ],
                    'video' => [
                        'class' => 'app\backend\modules\goods\models\GoodsVideo',
                        'function_validator' => 'relationValidator',
                        'function_save' => 'relationSave'
                    ],
                ],
                'order' => [
                    //订单操作记录
                    'order_operation_log' => [
                        'class' => 'app\backend\modules\order\models\OrderOperationLog',
                        'function_save' => 'insertOperationLog'
                    ]
                ]


            ],
            'goods'=>[
                'models' => [
                    'home_page' => \app\common\models\Goods::class,
                    'goods_info' => \app\common\models\Goods::class,
                    'goods_list' => \app\common\models\Goods::class,
                    'footprint' => \app\common\models\Goods::class,
                    'collection_page' => \app\common\models\Goods::class,
                    'commodity_classification' => \app\common\models\Goods::class,
                ],
            ],
            'shop-foundation' => [
                'goods' => [
                    'dealPrice' => [
                        [
                            'key' => 'goodsDealPrice',
                            'class' => function (\app\common\models\Goods $goods, $param = []) {
                                return new \app\common\modules\goods\dealPrice\GoodsDealPrice($goods);
                            },
                        ], [
                            'key' => 'marketDealPrice',
                            'class' => function (\app\common\models\Goods $goods, $param = []) {
                                return new \app\common\modules\goods\dealPrice\MarketDealPrice($goods);
                            },
                        ]
                    ],

                    //标准商城默认都会显示下面这几种类型的商品
                    'plugin' => [0],
                ],
                'member-cart' => [
                    'with' => []
                ],
                'model' => ['PreOrder' => [

                ]],
                'model-expansions' => [\app\frontend\models\Goods::class => [
                    \Yunshop\Love\Frontend\Models\Expansions\GoodsExpansions::class,
                    \Yunshop\AreaDividend\models\expansions\GoodsExpansions::class,
                    \Yunshop\Supplier\common\models\expansions\GoodsExpansions::class
                ]],
                'order' => ['member_order_operations' => [
                    'waitPay' => [
                        \app\frontend\modules\order\operations\member\Pay::class,
                        \app\frontend\modules\order\operations\member\Close::class,

                    ],
                    'waitSend' => [
                        \app\frontend\modules\order\operations\member\ApplyRefund::class,
                        \app\frontend\modules\order\operations\member\ContactCustomerService::class,
                        \app\frontend\modules\order\operations\member\Refunding::class,
                        \app\frontend\modules\order\operations\member\Refunded::class,
                        \app\frontend\modules\order\operations\member\Coupon::class, //分享优惠卷

                    ],
                    'waitReceive' => [
                        \app\frontend\modules\order\operations\member\ExpressInfo::class,
                        \app\frontend\modules\order\operations\member\Receive::class,
                        \app\frontend\modules\order\operations\member\ApplyRefund::class,
                        \app\frontend\modules\order\operations\member\ContactCustomerService::class,
                        \app\frontend\modules\order\operations\member\Refunding::class,
                        \app\frontend\modules\order\operations\member\Refunded::class,
                        \app\frontend\modules\order\operations\member\Coupon::class, //分享优惠卷
                    ],
                    'complete' => [
                        \app\frontend\modules\order\operations\member\ExpressInfo::class,
                        \app\frontend\modules\order\operations\member\Delete::class,
                        \app\frontend\modules\order\operations\member\ApplyRefund::class,
                        \app\frontend\modules\order\operations\member\ContactCustomerService::class,
                        \app\frontend\modules\order\operations\member\Refunding::class,
                        \app\frontend\modules\order\operations\member\Refunded::class,
                        \app\frontend\modules\order\operations\member\CheckInvoice::class,
                        \app\frontend\modules\order\operations\member\Coupon::class, //分享优惠卷

                    ],
                    'close' => [
                        \app\frontend\modules\order\operations\member\ExpressInfo::class,
                        \app\frontend\modules\order\operations\member\Delete::class,
                        \app\frontend\modules\order\operations\member\Refunded::class,
                    ],

                ],
                    'status' => [
                        0 => 'waitPay',
                        1 => 'waitSend',
                        2 => 'waitReceive',
                        3 => 'complete',
                        -1 => 'close',
                    ],],
                'order-delivery-method' => [
                    'shop' => [
                        'dispatch_type' => 1, //配送类型
                        'class' => 'app\common\models\DispatchType',//类
                        'function' => 'getExpressDelivery'//方法
                    ],],
                'order-fee' => [],
                'status' => [
//    'remittance'=>\app\common\modules\payType\remittance\models\status\RemittanceStatus::class,
//    'remittanceAudit'=>\app\common\modules\payType\remittance\models\status\RemittanceAuditStatus::class,
                    [
                        'key' => 'remittance',
                        'class' => \app\common\modules\payType\remittance\models\status\RemittanceStatus::class,
                    ],
                    [
                        'key' => 'remittanceAudit',
                        'class' => \app\common\modules\payType\remittance\models\status\RemittanceAuditStatus::class,
                    ],
                ],
                'goods-discount' => [
                    [
                        'weight' => 2010,
                        'class' => function (\app\frontend\modules\orderGoods\models\PreOrderGoods $preOrderGoods) {
                            return new \app\frontend\modules\orderGoods\discount\SingleEnoughReduce($preOrderGoods);
                        },
                    ], [
                        'weight' => 2020,
                        'class' => function (\app\frontend\modules\orderGoods\models\PreOrderGoods $preOrderGoods) {
                            return new \app\frontend\modules\orderGoods\discount\EnoughReduce($preOrderGoods);
                        },
                    ],
                ],
                'coupon' => [
                    'OrderCoupon' => [
                        'scope' => [
                            [
                                'key' => \app\common\models\Coupon::COUPON_GOODS_USE,
                                'class' => function ($coupon) {
                                    return new \app\frontend\modules\coupon\services\models\UseScope\GoodsScope($coupon);
                                },
                            ],
                            [
                                'key' => \app\common\models\Coupon::COUPON_CATEGORY_USE,
                                'class' => function ($coupon) {
                                    return new \app\frontend\modules\coupon\services\models\UseScope\CategoryScope($coupon);
                                },
                            ],
                            [
                                'key' => \app\common\models\Coupon::COUPON_SHOP_USE,
                                'class' => function ($coupon) {
                                    return new \app\frontend\modules\coupon\services\models\UseScope\ShopScope($coupon);
                                },
                            ],
                        ]
                    ]
                ], 'discount' => [
                    'GoodsMemberLevelDiscountCalculator' => [
                        [
                            'priority' => '1000',
                            'key' => 'goods',
                            'class' => function ($goods, $member) {
                                return new \app\common\modules\discount\GoodsMemberLevelDiscountCalculator($goods, $member);
                            }
                        ],
                        [
                            'priority' => '2000',
                            'key' => 'shop',
                            'class' => function ($goods, $member) {
                                return new \app\common\modules\discount\ShopGoodsMemberLevelDiscountCalculator($goods, $member);
                            }
                        ],
                    ]
                ],'goods-option' => [
                    'dealPrice' => [
                        [
                            'key' => 'goodsDealPrice',
                            'class' => function (\app\common\models\GoodsOption $goodsOption, $param = []) {
                                return new \app\common\modules\goodsOption\dealPrice\GoodsDealPrice($goodsOption);
                            },
                        ], [
                            'key' => 'marketDealPrice',
                            'class' => function (\app\common\models\GoodsOption $goodsOption, $param = []) {
                                return new \app\common\modules\goodsOption\dealPrice\MarketDealPrice($goodsOption);
                            },
                        ]
                    ]
                ],'order-discount' => [
                    [
                        'key' => 'singleEnoughReduce',
                        'class' => function (\app\frontend\modules\order\models\PreOrder $preOrder) {
                            return new \app\frontend\modules\order\discount\SingleEnoughReduce($preOrder);
                        },
                    ], [
                        'key' => 'enoughReduce',
                        'class' => function (\app\frontend\modules\order\models\PreOrder $preOrder) {
                            return new \app\frontend\modules\order\discount\EnoughReduce($preOrder);
                        },
                    ],
                    [
                        'key' => 'couponDiscount',
                        'class' => function (\app\frontend\modules\order\models\PreOrder $preOrder) {
                            return new \app\frontend\modules\order\discount\CouponDiscount($preOrder);
                        },
                    ], [
                        'key' => 'singleEnoughReduce',
                        'class' => function (\app\frontend\modules\order\models\PreOrder $preOrder) {
                            return new \app\frontend\modules\order\discount\SingleEnoughReduce($preOrder);
                        },
                    ], [
                        'key' => 'enoughReduce',
                        'class' => function (\app\frontend\modules\order\models\PreOrder $preOrder) {
                            return new \app\frontend\modules\order\discount\EnoughReduce($preOrder);
                        },
                    ],
                    [
                        'key' => 'couponDiscount',
                        'class' => function (\app\frontend\modules\order\models\PreOrder $preOrder) {
                            return new \app\frontend\modules\order\discount\CouponDiscount($preOrder);
                        },
                    ]
                ],'order-price-nodes' => [
                    [
                        'class' => function (\app\frontend\modules\order\models\PreOrder $order) {
                            return new \app\frontend\modules\order\OrderGoodsPriceNode($order, 1000);
                        },
                    ],

                    [
                        'class' => function (\app\frontend\modules\order\models\PreOrder $order) {
                            return new \app\frontend\modules\order\OrderDispatchPriceNode($order, 3000);
                        },
                    ],
                    [
                        'class' => function (\app\frontend\modules\order\models\PreOrder $order) {
                            return new \app\frontend\modules\order\OrderFeeNode($order, 9200);
                        },
                    ]
                ],
            ]
        ];

        $plugins = app('plugins')->getEnabledPlugins();
        foreach ($plugins as $plugin) {

            foreach ($plugin->app()->getShopConfigItems() as $key => $item) {
                array_set($result, $key, $item);
            }
        }
        return $result;
    }


    private function getItems()
    {
        if (!isset($this->items)) {
            $this->items = $this->_getItems();
        }
        return $this->items;
    }

    public function getItem($key)
    {
        return array_get($this->getItems(), $key);
    }

    public function clear()
    {
        $this->items = null;
    }

    public function get($key = null)
    {
        if (empty($key)) {
            return $this->getItems();
        }
        return $this->getItem($key);
    }

    public function set($key, $value = null)
    {
        $items = $this->getItems();
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                array_set($items, $k, $v);
            }
        } else {
            array_set($items, $key, $value);
        }
        $this->items = $items;
    }

    public function push($key, $value)
    {
        $all = $this->getItems();
        $array = $this->getItem($key) ?: [];
        $array[] = $value;
        array_set($all, $key, $array);
        $this->items = $all;

    }
}