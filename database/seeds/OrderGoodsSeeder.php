<?php

use Illuminate\Database\Seeder;

class OrderGoodsSeeder extends Seeder
{
    protected $sourceTable = 'sz_yi_order_goods';
    protected $table = 'yz_order_goods';
    protected $sourceGoodsTable = 'sz_yi_goods';
    protected $orderTable = 'yz_order';
//    protected $mappingOrderTable = 'yz_mapping_orders';
//    protected $mappingGoodTable = 'yz_mapping_goods'; //todo 等待"商品重构组"生成新旧goods_id的对应表

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        return;
        if (!Schema::hasTable($this->sourceTable)) {
            echo $this->sourceTable." 不存在 跳过\n";
            return;
        }
        if (!Schema::hasTable($this->sourceGoodsTable)) {
            echo $this->sourceGoodsTable." 不存在 跳过\n";
            return;
        }
        //检测新的数据表是否有数据
//        $newList = DB::table($this->table)->first();
//        if($newList){
//            echo $this->table."数据表已经有数据, 请检查.\n";
//            return;
//        }
//        print_r($_SERVER);

        $sourceList = \Illuminate\Support\Facades\DB::table($this->sourceTable)->chunk(100, function($records){
            foreach ($records as $record){

                //如果旧表字段没有值,则设为0
                foreach ($record as $k => $v){
                    if(!$v){
                        if ($k != 'total'){
                            $record[$k] = 0;
                        } else {
                            $record[$k] = 1; //新表中 total 字段默认为 1
                        }
                    }
                }

                //获取"商品名称"和"商品图片URL"
                $goodInfo = \Illuminate\Support\Facades\DB::table($this->sourceGoodsTable)->select('title','thumb')
                    ->where('id','=',$record['goodsid'])->first();
                if ($goodInfo){
                    $record['title'] = $goodInfo['title'];
                    if(preg_match('/^http.*/',$goodInfo['thumb'])){
                        $record['thumb'] = $goodInfo['thumb']; //todo 最后的url取决于迁徙后放在哪个路径
                    } else if (preg_match('/^images.*/',$goodInfo['thumb'])){
                        $record['thumb'] = 'http://demo.yunzshop.com'.'/attachment/'.$goodInfo['thumb']; //todo 最后的url取决于迁徙后放在哪个路径
                        //无法通过全局变量$_SERVER['SERVER_NAME']获取网站域名
                    }
                } else {
//                    echo '在旧的goods表中找不到该商品信息';
//                    return;
                    $record['title'] = 0; //todo 调试用
                    $record['thumb'] = 0; //todo 调试用
                }

                //获取orderid和member_id
//                $orderMapping = DB::table($this->mappingOrderTable)->select('new_order_id','new_member_id')
//                    ->where('old_order_id','=',$record['orderid'])->first();
//                if(!$orderMapping){
//                    echo $this->mappingOrderTable.'表中没有找到新旧orderid的对应关系,旧orderid为'.$record['orderid'];
//                    return;
//                }
//                $uid = $orderMapping['new_member_id'];
//                $orderId = $orderMapping['new_order_id'];
                $uid = 1; //todo 调试用
                $orderId = $record['orderid']; //todo 调试用

                //获取goods_id
//                $goodsId = DB::table($this->mappingGoodTable)->select('new_good_id')
//                    ->where('old_good_id','=',$record['goodsid'])->first();
//                if(!goodsId){
//                    echo $this->mappingGoodTable.'表中没有找到新旧goods_id的对应关系,旧goodsid为'.$record['goodsid'];
//                    return;
//                }
                $goodsId = $record['goodsid']; //todo 调试用

                \Illuminate\Support\Facades\DB::table($this->table)->insert(
                    [
                        'uniacid' => $record['uniacid'], //公众号ID
                        'order_id' => $orderId, //订单ID
                        'member_id' => $uid, //会员身份标识, mc_member表的uid
                        'goods_id' => $goodsId, //商品ID
                        'goods_sn' => $record['goodssn'], //商品编码 //todo productsn?
                        'goods_price' => $record['price'] * 100, //商品快照价格 (单位为"分")
                        'total' => $record['total'], //订单商品件数
                        'price' => $record['realprice'] * 100, //真实价格 (单位为"分")
                        'title' => $record['title'], //商品名称
                        'thumb' => $record['thumb'], //商品图片
                        'create_time' => $record['createtime'], //生成记录的时间
                    ]
                );
            }
        });
    }
}
