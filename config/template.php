<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/10
 * Time: 上午9:30
 */

return [
    'seller_pay' => [
        'title' => '卖家通知(会员付款通知)',
        'subtitle' => '会员付款通知',
        'value' => 'seller_pay',
        'param' => [
            '粉丝昵称', '订单号', '下单时间', '支付时间', '支付方式', '订单金额', '运费', '商品详情（含规格）', '收件人姓名', '收件人电话', '收件人地址'
        ]
    ],
    'seller_refund_apply' => [
        'title' => '卖家通知(会员申请退款/退货/换货)',
        'subtitle' => '会员申请退款退货通知商家',
        'value' => 'seller_refund_apply',
        'param' => [
            '商城名称'  ,'粉丝昵称' , '退款单号', '退款申请时间' ,'退款类型', '退款金额', '退款方式', '退款原因', '商品详情（含规格）'
        ]
    ],
    'seller_receipt' => [
        'title' => '卖家通知(会员确认收货通知)',
        'subtitle' => '会员确认收货通知',
        'value' => 'seller_receipt',
        'param' => [
            '粉丝昵称', '订单号', '确认收货时间', '运费', '商品详情（含规格）', '收件人姓名', '收件人电话', '收件人地址'
        ]
    ],
    'buyer_order_create_success' => [
        'title' => '买家通知(订单提交成功通知)',
        'subtitle' => '订单提交成功通知',
        'value' => 'buyer_order_create_success',
        'param' => [
            '商城名称', '粉丝昵称', '订单号', '下单时间', '订单金额', '运费', '商品详情（含规格）'
        ]
    ],
    'buyer_order_pay_success' => [
        'title' => '买家通知(订单支付成功通知)',
        'subtitle' => '订单支付成功通知',
        'value' => 'buyer_order_pay_success',
        'param' => [
            '商城名称', '粉丝昵称', '订单号', '下单时间', '订单金额', '运费', '商品详情（含规格）', '支付方式', '支付时间'
        ]
    ],
    'buyer_order_sending' => [
        'title' => '买家通知(订单发货通知)',
        'subtitle' => '订单发货通知',
        'value' => 'buyer_order_sending',
        'param' => [
            '商城名称', '粉丝昵称', '订单号', '下单时间', '订单金额', '运费', '商品详情（含规格）', '发货时间', '快递公司', '快递单号'
        ]
    ],
    'buyer_order_receipt_success' => [
        'title' => '买家通知(订单确认收货通知)',
        'subtitle' => '订单确认收货通知',
        'value' => 'buyer_order_receipt_success',
        'param' => [
            '商城名称', '粉丝昵称', '订单号', '下单时间', '订单金额', '运费', '商品详情（含规格）', '确认收货时间'
        ]
    ],
    'buyer_order_cancle_success' => [
        'title' => '买家通知(订单取消通知)',
        'subtitle' => '订单取消通知',
        'value' => 'buyer_order_cancle_success',
        'param' => [
            '商城名称', '粉丝昵称', '订单号', '下单时间', '订单金额', '运费', '商品详情（含规格）', '支付方式', '订单取消时间'
        ]
    ],
    'buyer_refund_apply' => [
        'title' => '买家通知(退款申请通知)',
        'subtitle' => '退款申请通知',
        'value' => 'buyer_refund_apply',
        'param' => [
            '商城名称', '粉丝昵称', '退款单号', '退款申请时间', '退款方式', '退款金额', '退款原因'
        ]
    ],
    'buyer_refund_apply_success' => [
        'title' => '买家通知(退款成功通知)',
        'subtitle' => '退款成功通知',
        'value' => 'buyer_refund_apply_success',
        'param' => [
            '商城名称', '粉丝昵称', '退款单号', '退款申请时间', '退款成功时间', '退款方式', '退款金额', '退款原因'
        ]
    ],
    'buyer_refund_apply_reject' => [
        'title' => '买家通知(退款申请驳回通知)',
        'subtitle' => '退款申请驳回通知',
        'value' => 'buyer_refund_apply_reject',
        'param' => [
            '商城名称', '粉丝昵称', '退款单号', '退款申请时间', '退款方式', '退款金额', '退款原因', '驳回原因'
        ]
    ],
    'buyer_order_status_change' => [
        'title' => '买家通知(订单状态更新)',
        'subtitle' => '订单状态更新',
        'value' => 'buyer_order_status_change',
        'param' => [
            '商城名称', '粉丝昵称', '订单号', '下单时间', '订单金额', '运费', '商品详情（含规格）', '支付方式', '原订单价格', '修改后订单价格', '修改时间'
        ]
    ],
    'order_parent_notice' => [
        'title' => '订单两级消息通知',
        'subtitle' => '下级订单通知',
        'value' => 'order_parent_notice',
        'param' => [
            '下级昵称', '下级层级', '订单状态', '订单号', '订单金额',
        ]
    ],
    'member_upgrade' => [
        'title' => '会员(会员升级)',
        'subtitle' => '会员升级',
        'value' => 'member_upgrade',
        'param' => [
            '粉丝昵称', '旧等级', '新等级', '时间', '有效期',
        ]
    ],
    'member_agent' => [
        'title' => '会员(获得推广权限通知)',
        'subtitle' => '获得推广权限通知',
        'value' => 'member_agent',
        'param' => [
            '昵称', '时间'
        ]
    ],
    'member_new_lower' => [
        'title' => '会员(新增下线通知)',
        'subtitle' => '新增下线通知',
        'value' => 'member_new_lower',
        'param' => [
            '昵称', '时间', '下级昵称'
        ]
    ],
    'finance_balance_change' => [
        'title' => '财务(余额变动通知)',
        'subtitle' => '余额变动通知',
        'value' => 'finance_balance_change',
        'param' => [
            '商城名称', '昵称', '时间', '余额变动金额', '余额变动类型', '变动后余额数值'
        ]
    ],
    'finance_point_change' => [
        'title' => '财务(积分变动通知)',
        'subtitle' => '积分变动通知',
        'value' => 'finance_point_change',
        'param' => [
            '商城名称', '昵称', '时间', '积分变动金额', '积分变动类型', '变动后积分数值'
        ]
    ],

    'finance_balance_deficiency' => [
        'title' => '财务(余额不足通知)',
        'subtitle' => '余额不足通知',
        'value' => 'finance_balance_deficiency',
        'param' => [
            '商城名称', '昵称', '时间', '通知额度', '当前余额'
        ]
    ],
    'finance_point_deficiency' => [
        'title' => '财务(积分不足通知)',
        'subtitle' => '积分不足通知',
        'value' => 'finance_point_deficiency',
        'param' => [
            '商城名称', '昵称', '时间', '通知额度', '当前积分'
        ]
    ],



    'finance_income_withdraw' => [
        'title' => '财务(提现申请通知)',
        'subtitle' => '提现申请通知',
        'value' => 'finance_income_withdraw',
        'param' => [
            '昵称', '时间', '收入类型', '金额', '手续费', '提现方式'
        ]
    ],
    'finance_income_withdraw_check' => [
        'title' => '财务(提现审核通知)',
        'subtitle' => '提现审核通知',
        'value' => 'finance_income_withdraw_check',
        'param' => [
            '昵称', '时间', '收入类型', '状态', '金额', '手续费', '审核通过金额', '提现方式'
        ]
    ],
    'finance_income_withdraw_pay' => [
        'title' => '财务(提现打款通知)',
        'subtitle' => '提现打款通知',
        'value' => 'finance_income_withdraw_pay',
        'param' => [
            '昵称', '时间', '收入类型', '状态', '金额', '提现方式'
        ]
    ],
    'finance_income_withdraw_arrival' => [
        'title' => '财务(提现到账通知)',
        'subtitle' => '提现到账通知',
        'value' => 'finance_income_withdraw_arrival',
        'param' => [
            '昵称', '时间', '收入类型', '状态', '金额', '提现方式'
        ]
    ],

    'finance_balance_withdraw_submit' => [
        'title' => '余额(提现提交通知)',
        'subtitle' => '余额提现提交通知',
        'value' => 'finance_balance_withdraw_submit',
        'param' => [
            '时间', '金额', '手续费'
        ]
    ],


    'finance_balance_withdraw_success' => [
        'title' => '余额(提现成功通知)',
        'subtitle' => '余额提现成功通知',
        'value' => 'finance_balance_withdraw_success',
        'param' => [
            '时间', '金额', '手续费'
        ]
    ],

    'finance_balance_withdraw_fail' => [
        'title' => '余额(提现失败通知)',
        'subtitle' => '余额提现失败通知',
        'value' => 'finance_balance_withdraw_fail',
        'param' => [
            '时间', '金额', '手续费', '提现单号'
        ]
    ],
    'coupon_expire' => [
        'title' => '优惠券(优惠券过期提醒)',
        'subtitle' => '优惠券过期提醒',
        'value' => 'coupon_expire',
        'param' => [
            '优惠券名称', '优惠券使用范围', '过期时间'
        ]
    ],
    'buy_goods_message' => [
        'title' => '购买商品通知',
        'subtitle' => '购买商品通知',
        'value' => 'buy_goods_message',
        'param' => [
            '订单编号', '商品名称（含规格）', '会员昵称', '商品金额', '商品数量', '订单状态', '时间'
        ]
    ],
    'coupon_obtain' => [
        'title' => '优惠券(获得优惠券通知)',
        'subtitle' => '获得优惠券通知',
        'value' => 'coupon_obtain',
        'param' => [
            '优惠券名称', '优惠券使用范围', '优惠券使用条件','优惠方式', '过期时间'
        ]
    ],
    'courier_pass' => [
        'title' => '快递单审核通过',
        'subtitle' => '快递单审核通过通知',
        'value' => 'courier_pass',
        'param' => [
            '会员昵称', '时间'
        ]
    ],
    'courier_back' => [
        'title' => '快递单审核驳回',
        'subtitle' => '快递单审核驳回通知',
        'value' => 'courier_back',
        'param' => [
            '会员昵称', '时间'
        ]
    ],

    'member_withdraw' => [
        'title' => '会员提现(管理员通知)',
        'subtitle' => '会员提现提交通知',
        'value' => 'member_finance_balance_submit',
        'param' => [
            '粉丝昵称', '申请时间','提现金额', '提现类型','提现方式'
        ]
    ],

    'universal_card_open' => [
        'title' => '一卡通开通',
        'subtitle' => '一卡通开通通知',
        'value' => 'universal_card_open',
        'param' => [
            '昵称', '时间','会员等级','有效期'
        ]
    ],
    'universal_card_expire' => [
        'title' => '一卡通权益到期',
        'subtitle' => '一卡通权益到期通知',
        'value' => 'universal_card_expire',
        'param' => [
            '昵称', '时间','会员等级','过期时间'
        ]
    ],
    'consumption_points' => [
        'title' => '消费积分【变动通知】',
        'subtitle' => '消费积分变动通知',
        'value' => 'consumption_points',
        'param' => [
            '昵称', '时间','业务类型','变动数量','当前剩余值'
        ]
    ],
    'subplatform' => [
        'title' => '子平台【业务通知】',
        'subtitle' => '子平台业务通知',
        'value' => 'subplatform',
        'param' => [
            '昵称', '时间','业务类型'
        ]
    ],
    'main_platform' => [
        'title' => '主平台通知【业务通知】',
        'subtitle' => '主平台通知业务通知',
        'value' => 'main_platform',
        'param' => [
            '昵称', '时间','业务类型'
        ]
    ],

    'settled_in' => [
        'title' => '供货平台(子平台入驻申请通知)',
        'subtitle' => '供货平台(子平台入驻申请通知)',
        'value' => 'settled_in',
        'param' => [
            '子平台名称','管理员昵称','时间'
        ]
    ],

//    /**
//     * 子平台入驻申请通知模板
//     */
//    'template.platform_residence_application'=> [
//        'title' => "子平台入驻申请通知",
//        'subtitle' => '子平台入驻申请通知',
//        'value' => 'platform_residence_application',
//        'param' => [
//            '昵称', '时间'
//        ]
//    ],

/**
 * 主平台-采购单生成通知模板
 */
    'template.purchasing_order_generation'=> [
    'title' => "主平台(采购单生成通知)",
    'subtitle' => '主平台(采购单生成通知)',
    'value' => 'purchasing_order_generation',
    'param' => [
        '销售平台名称', '供应商平台名称','订单号','下单时间','订单金额','商品标题'
        ]
    ],

/**
 * 采购单支付通知模板
 */
    'template.purchasing_order_pay'=> [
    'title' => "主平台(采购单支付通知)",
    'subtitle' => '主平台(采购单支付通知)',
    'value' => 'purchasing_order_pay',
    'param' => [
        '销售平台名称', '供应商平台名称','订单号','下单时间','订单金额','商品标题','支付时间'
        ]
    ],

/**
 * 主平台-供货单发货通知模板
 */
    'template.supply_delivery'=> [
    'title' => "主平台(供货单发货通知)",
    'subtitle' => '主平台(供货单发货通知)',
    'value' => 'supply_delivery',
    'param' => [
        '销售平台名称', '供应商平台名称','粉丝昵称','订单号','下单时间','订单金额','商品标题','发货时间','快递公司','快递单号'
,        ]
    ],

/**主平台-供货单完成通知
 * 模板
 */
    'template.supply_complete'=> [
    'title' => "主平台(供货单完成通知)",
    'subtitle' => '主平台(供货单完成通知)',
    'value' => 'supply_complete',
    'param' => [
        '销售平台名称', '供应商平台名称','粉丝昵称','订单号','下单时间','订单金额','商品标题','确认收货时间'
        ]
    ],

/**
 * 子平台提现通知模板
 */
    'template.subplatform_presentation'=> [
    'title' => "供货平台(子平台提现通知)",
    'subtitle' => '供货平台(子平台提现通知)',
    'value' => 'subplatform_presentation',
    'param' => [
        '平台名称', '时间','提现金额','提现方式'
        ]
    ],

/**
 * 审核通过通知模板
 */
    'template.audit_pass'=> [
    'title' => "供货平台(审核通过通知)",
    'subtitle' => '供货平台(审核通过通知)',
    'value' => 'audit_pass',
    'param' => [
        '粉丝昵称', '子平台名称','通过时间'
        ]
    ],

/**
 * 审核驳回通知模板
 */
    'template.audit_rejected'=> [
    'title' => "供货平台(审核驳回通知)",
    'subtitle' => '供货平台(审核驳回通知)',
    'value' => 'audit_rejected',
    'param' => [
        '粉丝昵称', '子平台名称','驳回时间'
        ]
    ],

/**
 * 采购单生成通知模板
 */
    'template.purchasing_order_generation_subplatform'=> [
    'title' => "采购单生成通知",
    'subtitle' => '采购单生成通知',
    'value' => 'purchasing_order_generation_subplatform',
    'param' => [
        '主平台名称', '粉丝昵称','订单号','下单时间','订单金额','商品标题'
        ]
    ],

/**
 * 采购单支付成功通知模板
 */
    'template.purchasing_order_pay_subplatform' => [
    'title' => "采购单支付成功通知",
    'subtitle' => '采购单支付成功通知',
    'value' => 'purchasing_order_pay_subplatform',
    'param' => [
        '主平台名称','粉丝昵称','订单号','下单时间','订单金额','商品标题','支付时间'
        ]
    ],

/**
 * 供货单发货通知模板
 */
    'template.dupply_delivery_subplatform'=> [
    'title' => "供货单发货通知",
    'subtitle' => '供货单发货通知',
    'value' => 'dupply_delivery_subplatform',
    'param' => [
        '主平台名称', '粉丝昵称','订单号','下单时间','订单金额','运费','商品标题','发货时间','快递公司','快递单号'
        ]
    ],

/**
 * 供货单完成通知模板
 */
    'template.completion_supply_order_subplatform'=> [
    'title' => "供货单完成通知",
    'subtitle' => '供货单完成通知',
    'value' => 'completion_supply_order_subplatform',
    'param' => [
        '主平台名称', '粉丝昵称','订单号','下单时间', '订单金额','商品标题','确认收货时间'
        ]
    ],

/**
 * 销售订单发货通知模板
 */
    'template.sales_order_delivery_subplatform'=> [
    'title' => "销售订单发货通知",
    'subtitle' => '销售订单发货通知',
    'value' => 'sales_order_delivery_subplatform',
    'param' => [
        '主平台名称', '粉丝昵称','订单号','下单时间', '订单金额','运费','商品标题','发货时间', '快递公司','快递单号'
        ]
    ],

/**
 * 销售订单完成通知模板
 */
    'template.sales_order_completion_subplatform'=> [
    'title' => "销售订单完成通知",
    'subtitle' => '销售订单完成通知',
    'value' => 'sales_order_completion_subplatform',
    'param' => [
        '主平台名称', '粉丝昵称','订单号','下单时间','订单金额', '商品标题','确认收货时间'
        ]
    ],

/**
 * 提现申请通知模板
 */
    'template.cash_withdrawal_application_subplatform'=> [
    'title' => "供货平台(提现申请通知)",
    'subtitle' => '供货平台(提现申请通知)',
    'value' => 'cash_withdrawal_application_subplatform',
    'param' => [
        '提现单号', '提现金额','昵称','子平台名称', '申请时间'
        ]
    ],

/**
 * 提现审核通过通知模板
 */
    'template.presentation_approval_subplatform'=> [
    'title' => "供货平台(提现审核通过通知)",
    'subtitle' => '供货平台(提现审核通过通知)',
    'value' => 'presentation_approval_subplatform',
    'param' => [
        '提现单号', '提现金额','昵称', '子平台名称', '审核时间'
        ]
    ],

/**
 * 提现驳回通知模板
 */
    'template.dismissal_subplatform'=> [
    'title' => "供货平台(提现驳回通知)",
    'subtitle' => '供货平台(提现驳回通知)',
    'value' => 'dismissal_subplatform',
    'param' => [
        '提现单号', '提现金额','昵称','子平台名称','驳回时间'
        ]
    ],

/**
 * 提现打款通知模板
 */
    'template.cash_withdrawals_subplatform'=> [
    'title' => "供货平台(提现打款通知)",
    'subtitle' => '供货平台(提现打款通知)',
    'value' => 'cash_withdrawals_subplatform',
    'param' => [
        '提现单号', '提现金额','昵称','子平台名称','打款时间'
        ]
    ],

/**
 * 提现到账通知模板
 */
    'template.cash_withdrawal_accoun_subplatform'=> [
    'title' => "供货平台(提现到账通知)",
    'subtitle' => '供货平台(提现到账通知)',
    'value' => 'cash_withdrawal_accoun_subplatform',
    'param' => [
        '提现单号', '提现金额','昵称','子平台名称','到账时间'
        ]
    ],
    /**
     * 收入提现失败通知
     */
    'finance_income_withdraw_fail' => [
        'title' => '提现失败管理员通知',
        'subtitle' => '提现失败管理员通知',
        'value' => 'finance_income_withdraw_fail',
        'param' => [
            '时间', '金额', '手续费', '提现单号'
        ]
    ],
    
    /*$data = [
        [
            'name' => '粉丝昵称',
            'value' => '杨洋'
        ],
        [
            'name' => '时间',
            'value' => '2017-11-10'
        ],
        [
            'name' => '下级会员昵称',
            'value' => '沈阳'
        ],
    ]*/
];

