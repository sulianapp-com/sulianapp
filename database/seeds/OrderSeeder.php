<?php

use Illuminate\Database\Seeder;

Class OrderSeeder extends Seeder
{
    protected $sourceTable = 'sz_yi_order';
    protected $memberMappingTable = 'mc_mapping_fans';
//    protected $newMappingMemberTable = 'new_mapping_fans'; //todo 针对原来的自编openid用户的会员, 等待"会员重构组"生成新旧openid的对应表
    protected $orderTable = 'yz_order';
    protected $orderMappingTable = 'yz_order_mapping'; //用来记录新旧orderid的对应关系,包括新旧uid的对应关系

    public function run()
    {
        return;
        if (!Schema::hasTable($this->sourceTable)) {
            echo $this->sourceTable." 不存在 跳过\n";
            return;
        }
        //检测新的数据表是否有数据
//        $newList = DB::table($this->orderTable)->first();
//        if($newList){
//            echo $this->orderTable . "数据表已经有数据, 请检查.\n";
//            return;
//        }

        $sourceList = \Illuminate\Support\Facades\DB::table($this->sourceTable)->chunk(100, function($records){
            foreach ($records as $record){

                //如果旧表字段没有值,则设为0
                foreach ($record as $k => $v){
                    if(!$v){
                        $record[$k] = 0;
                    }
                }

                //"删除时间"在旧表中没有对应字段, 只有"是否删除"字段
                //如果该订单删除, 则将"取消订单时间"设置为该订单相关时间的最大值 //todo
                $recentTime = max($record['createtime'], $record['finishtime'],
                                  $record['paytime'], $record['sendtime'], $record['canceltime']);
                if ($record['deleted'] != 0){
                    $record['deleted_at'] = $recentTime;
                } else {
                    $record['deleted_at'] = 0;
                }

                //"updated_at"在旧表中没有对应字段, 取该订单相关时间的最大值 //todo
                $record['updated_at']  = $recentTime;

                //旧表中"openid"和微擎"mc_member"表的uid的对应
                //微信登录用户的openid和uid的对应关系, 借助数据表mc_mapping_fans
                //手机注册用户的openid和uid的对应关系, 借助会员迁移后生成的临时表
                if (preg_match('/^o.*/', $record['openid'])){ //以o开头的就是微信登录用户
                    $uid = \Illuminate\Support\Facades\DB::table($this->memberMappingTable)->select('uid')
                           ->where('openid','=',$record['openid'])->first();
                    if($uid){
                        $record['uid'] = $uid['uid'];
                    } else {
//                        echo "mc_mapping_fans表内缺少用户".$record['openid']."的uid"."\n";
//                        exit;
                        $record['uid'] = 0; //todo 调试用
                    }
//                } else if (preg_match('/^u.*/', $record['openid'])){ //以u开头的就是手机注册用户
//                    $uid = DB::table($this->newMappingMemberTable) //todo 等待"会员重构组"的结果
//                                     ->where('openid','=',$record['openid'])->first();
//                      if($uid){
//                          $record['uid'] = $uid;
//                      } else {
//                          echo 'new_mapping_fans表内缺少该用户的uid'."\n";
//                          exit;
//                      }
                } else if(preg_match('/^u.*/', $record['openid'])){
                    $record['uid'] = 0; //todo 临时调试用, 等待"会员重构组"针对原来的自编openid用户的会员的mapping数据
                } else {
//                    echo '获取 uid 时产生非预期结果, 请检查错误'."\n";
//                    return;
                    $record['uid'] = 0; //todo 临时调试用
                }

                $newOrderId = \Illuminate\Support\Facades\DB::table($this->orderTable)->insertGetId(
                    [
                        'uniacid' => $record['uniacid'], //公众号ID
                        'member_id' => $record['uid'], //mc_member的uid
                        'order_sn' => $record['ordersn'], //订单号
                        'price' => $record['price'] * 100, //订单金额 (单位为"分")
                        'goods_price' => $record['goodsprice'] * 100, //商品金额 (单位为"分")
                        'status' => $record['status'], //订单状态(-1为订单取消，0为待支付，1为已付款，2为已发货，3为已完成)
                        'create_time' => $record['createtime'], //下单时间
                        'is_deleted' => $record['deleted'], //是否删除
                        'is_member_deleted' => $record['userdeleted'], //是否用户删除
                        'finish_time' => $record['finishtime'], //交易完成时间
                        'pay_time' => $record['paytime'], //支付时间
                        'send_time' => $record['sendtime'], //发货时间
                        'cancel_time' => $record['canceltime'], //取消订单时间
                        'created_at' => $record['createtime'], //框架自动记录时间
                        'updated_at' => $record['updated_at'], //框架自动记录时间
                        'deleted_at' => $record['deleted_at'], //框架自动记录时间
                        'dispatch_type_id' => $record['dispatchid'], //配送方式ID
                        'pay_type_id' => $record['paytype'], //支付方式ID (1为余额，2为在线，3为到付)
                    ]
                );

                \Illuminate\Support\Facades\DB::table($this->orderMappingTable)->insert(
                    [
                        'old_order_id' => $record['id'],
                        'new_order_id' => $newOrderId,
                        'old_openid' => $record['openid'],
                        'new_member_id' =>$record['uid']
                    ]
                );
            }
        });
    }

}