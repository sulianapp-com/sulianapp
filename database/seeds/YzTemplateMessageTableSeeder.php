<?php

use Illuminate\Database\Seeder;

class YzTemplateMessageTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \Illuminate\Support\Facades\DB::table('yz_template_message')->delete();
        
        \Illuminate\Support\Facades\DB::table('yz_template_message')->insert(array (
            0 => 
            array (
                'id' => 1,
                'type' => 'system',
                'item' => 'task_process',
                'parent_item' => '',
                'title' => '任务处理通知',
                'template_id_short' => 'OPENTM200605630',
                'template_id' => '',
                'content' => '{{first.DATA}}
任务名称：{{keyword1.DATA}}
通知类型：{{keyword2.DATA}}
{{remark.DATA}}',
                'example' => '您好，您有新的待办任务
任务名称：张三申请年假3天
通知类型：待办
请抽空处理',
                'status' => 0,
            ),
            1 => 
            array (
                'id' => 2,
                'type' => 'system',
                'item' => 'order_created',
                'parent_item' => '',
                'title' => '订单生成通知',
                'template_id_short' => 'OPENTM205213550',
                'template_id' => '',
                'content' => '{{first.DATA}}
时间：{{keyword1.DATA}}
商品名称：{{keyword2.DATA}}
订单号：{{keyword3.DATA}}
{{remark.DATA}}',
                'example' => '订单生成通知
时间：2014年7月21日 18:36
商品名称：苹果
订单号：007
订单成功',
                'status' => 0,
            ),
            2 => 
            array (
                'id' => 3,
                'type' => 'system',
                'item' => 'order_submit_success',
                'parent_item' => '',
                'title' => '订单提交成功通知',
                'template_id_short' => 'OPENTM200746866',
                'template_id' => '',
                'content' => '{{first.DATA}}
店铺：{{keyword1.DATA}}
下单时间：{{keyword2.DATA}}
商品：{{keyword3.DATA}}
金额：{{keyword4.DATA}}
{{remark.DATA}}',
                'example' => '您的订单已提交成功
店铺：有间便利店
下单时间：2014-10-31 19:44:51
商品：软装经典 3份
金额：¥33.0
您的订单我们已经收到，配货后将尽快配送~',
                'status' => 0,
            ),
            3 => 
            array (
                'id' => 4,
                'type' => 'system',
                'item' => 'self_order_submit_success',
                'parent_item' => '',
                'title' => '自提订单提交成功通知',
                'template_id_short' => 'OPENTM201594720',
                'template_id' => '',
                'content' => '{{first.DATA}}
自提码：{{keyword1.DATA}}
商品详情：{{keyword2.DATA}}
提货地址：{{keyword3.DATA}}
提货时间：{{keyword4.DATA}}
{{remark.DATA}}',
                'example' => '您的订单已提交成功！
自提码：140987567809
商品详情：水杯，1个，2元
提货地址：朝阳区方恒国际C座宝诚发超市
提货时间：2014-11-6 12:08 至 2014-11-8 15:00
客服电话：4008-888-888',
                'status' => 0,
            ),
            4 => 
            array (
                'id' => 5,
                'type' => 'system',
                'item' => 'order_cancel',
                'parent_item' => '',
                'title' => '订单取消通知',
                'template_id_short' => 'TM00850',
                'template_id' => '',
                'content' => '{{first.DATA}} 
订单金额：{{orderProductPrice.DATA}} 
商品详情：{{orderProductName.DATA}} 
收货信息：{{orderAddress.DATA}} 
订单编号：{{orderName.DATA}} 
{{remark.DATA}}',
                'example' => '您的京东订单已取消 
订单金额：44.20元 
商品详情：七匹狼真皮鞋包七匹狼真皮鞋包 
收货信息：深圳市宝安区海秀路23号
订单编号：8976541236598771 
点击“详情”查看详细处理信息，如有疑问可直接回复此公众号联系京东客服。 
详情 &gt;',
                'status' => 0,
            ),
            5 => 
            array (
                'id' => 6,
                'type' => 'system',
                'item' => 'order_pay_success',
                'parent_item' => '',
                'title' => '订单支付成功通知',
                'template_id_short' => 'OPENTM204987032',
                'template_id' => '',
                'content' => '{{first.DATA}}
订单：{{keyword1.DATA}}
支付状态：{{keyword2.DATA}}
支付日期：{{keyword3.DATA}}
商户：{{keyword4.DATA}}
金额：{{keyword5.DATA}}
{{remark.DATA}}',
                'example' => '您已支付成功订单，请稍后，我们正在为您制作可口的菜品！
订单：街觅 10010
支付状态：支付成功
支付日期：2015-05-26
商户：街觅
金额：100元
【街觅】欢迎您的再次到来！',
                'status' => 0,
            ),
            6 => 
            array (
                'id' => 7,
                'type' => 'system',
                'item' => 'order_deliver',
                'parent_item' => '',
                'title' => '订单发货通知',
                'template_id_short' => 'OPENTM202243318',
                'template_id' => '',
                'content' => '{{first.DATA}}
订单内容：{{keyword1.DATA}}
物流服务：{{keyword2.DATA}}
快递单号：{{keyword3.DATA}}
收货信息：{{keyword4.DATA}}
{{remark.DATA}}',
                'example' => '嗖嗖嗖，您的证照和回执已发货，我们正加速送到您的手上。
订单内容：证件照 （居民身份证）
物流服务：顺丰即日达
快递单号：XW5244030005646
收货信息：陈璐 广东省 广州市 天河区 科韵北路112号
请您耐心等候。',
                'status' => 0,
            ),
            7 => 
            array (
                'id' => 8,
                'type' => 'system',
                'item' => 'order_confirm_receipt',
                'parent_item' => '',
                'title' => '订单确认收货通知',
                'template_id_short' => 'OPENTM202314085',
                'template_id' => '',
                'content' => '{{first.DATA}}
订单号：{{keyword1.DATA}}
商品名称：{{keyword2.DATA}}
下单时间：{{keyword3.DATA}}
发货时间：{{keyword4.DATA}}
确认收货时间：{{keyword5.DATA}}
{{remark.DATA}}',
                'example' => '亲：您在我们商城买的宝贝已经确认收货。
订单号：323232323232
商品名称：最新款男鞋
下单时间：2015 01 01 12:00
发货时间：2015 01 01 14:00
确认收货时间：2015 01 02 14:00
感谢您的支持与厚爱。',
                'status' => 0,
            ),
            8 => 
            array (
                'id' => 9,
                'type' => 'system',
                'item' => 'refund_apply',
                'parent_item' => '',
                'title' => '退款申请通知',
                'template_id_short' => 'TM00431',
                'template_id' => '',
                'content' => '{{first.DATA}}

退款金额：{{orderProductPrice.DATA}}
商品详情：{{orderProductName.DATA}}
订单编号：{{orderName.DATA}}
{{remark.DATA}}',
                'example' => '您已申请退款，等待商家确认退款信息。

退款金额：¥145.25
商品详情：七匹狼正品 牛皮男士钱包 真皮钱…
订单编号：546787944-55446467-544749

可通过电脑进入“QQ网购-帮助中心-投诉退款与举报”了解更多维权信息。     ',
                'status' => 0,
            ),
            9 => 
            array (
                'id' => 10,
                'type' => 'system',
                'item' => 'refund_success',
                'parent_item' => '',
                'title' => '退款成功通知',
                'template_id_short' => 'TM00430',
                'template_id' => '',
                'content' => '{{first.DATA}}

退款金额：{{orderProductPrice.DATA}}
商品详情：{{orderProductName.DATA}}
订单编号：{{orderName.DATA}}
{{remark.DATA}}',
                'example' => '您的订单已经完成退款，¥145.25已经退回您的付款账户，请留意查收。

退款金额：¥145.25
商品详情：七匹狼正品 牛皮男士钱包 真皮钱…
订单编号：546787944-55446467-544749',
                'status' => 0,
            ),
            10 => 
            array (
                'id' => 11,
                'type' => 'system',
                'item' => 'refund_reject',
                'parent_item' => '',
                'title' => '退款申请驳回通知',
                'template_id_short' => 'TM00432',
                'template_id' => '',
                'content' => '{{first.DATA}}

退款金额：{{orderProductPrice.DATA}}
商品详情：{{orderProductName.DATA}}
订单编号：{{orderName.DATA}}
{{remark.DATA}}',
                'example' => '您的退款申请被商家驳回，可与商家协商沟通。
&gt;&gt;查看驳回理由

退款金额：¥145.25
商品详情：七匹狼正品 牛皮男士钱包 真皮钱…
订单编号：546787944-55446467-544749

可通过电脑进入“QQ网购-帮助中心-投诉退款与举报”了解更多维权信息。',
                'status' => 0,
            ),
            11 => 
            array (
                'id' => 12,
                'type' => 'system',
                'item' => 'member_upgrade',
                'parent_item' => 'task_process',
                'title' => '会员升级通知',
                'template_id_short' => '',
                'template_id' => '',
                'content' => '',
                'example' => '',
                'status' => 0,
            ),
            12 => 
            array (
                'id' => 13,
                'type' => 'system',
                'item' => 'recharge_success',
                'parent_item' => '',
                'title' => '充值成功通知',
                'template_id_short' => 'TM00977',
                'template_id' => '',
                'content' => '{{first.DATA}}

充值金额:{{money.DATA}}
充值方式:{{product.DATA}}
{{remark.DATA}}',
                'example' => '成功充值理财通余额

充值金额:1000.00元
充值方式:工商银行(尾号4593)/pc大额充值',
                'status' => 0,
            ),
            13 => 
            array (
                'id' => 14,
                'type' => 'system',
                'item' => 'recharge_refund',
                'parent_item' => '',
                'title' => '充值退款通知',
                'template_id_short' => 'TM00004',
                'template_id' => '',
                'content' => '{{first.DATA}}

退款原因：{{reason.DATA}}
退款金额：{{refund.DATA}}
{{remark.DATA}}',
                'example' => '您好，您对微信影城影票的抢购未成功，已退款。

退款原因：未抢购成功
退款金额：70元
备注：如有疑问，请致电13912345678联系我们，或回复M来了解详情。',
                'status' => 0,
            ),
            14 => 
            array (
                'id' => 15,
                'type' => 'system',
                'item' => 'withdraw_submit',
                'parent_item' => '',
                'title' => '提现提交通知',
                'template_id_short' => 'TM00979',
                'template_id' => '',
                'content' => '{{first.DATA}}

提现金额:{{money.DATA}}
提现时间:{{timet.DATA}}
{{remark.DATA}}',
                'example' => '理财通余额提现申请已提交，资金预计XX月XX日24:00前到账，请注意查收。

提现金额:1000.00元
提现时间:2014-04-02 11:45:08',
                'status' => 0,
            ),
            15 => 
            array (
                'id' => 16,
                'type' => 'system',
                'item' => 'withdraw_success',
                'parent_item' => '',
                'title' => '提现成功通知',
                'template_id_short' => 'TM00980',
                'template_id' => '',
                'content' => '{{first.DATA}}

提现金额:{{money.DATA}}
提现时间:{{timet.DATA}}
{{remark.DATA}}',
                'example' => '理财通余额资金已到账

提现金额:1000.00元
到账时间:2014-04-02 11:45:08',
                'status' => 0,
            ),
            16 => 
            array (
                'id' => 17,
                'type' => 'system',
                'item' => 'withdraw_fail',
                'parent_item' => '',
                'title' => '提现失败通知',
                'template_id_short' => 'TM00981',
                'template_id' => '',
                'content' => '{{first.DATA}}

提现金额:{{money.DATA}} 
提现时间:{{time.DATA}}
{{remark.DATA}} ',
                'example' => '理财通余额提现失败，已将资金退回至理财通余额，点击【详情】查看取出失败原因。如有疑问请联系客服0755-86010333

提现金额:1000.00元
提现时间:2014-04-02 11:45:08',
                'status' => 0,
            ),
        ));
        
        
    }
}