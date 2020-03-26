<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2019/1/12
 * Time: 15:05
 */

namespace app\Jobs;


use app\backend\modules\charts\modules\team\models\MemberMonthOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OrderMemberMonthJob implements  ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function handle()
    {

        $time = time();
        $nowyear = date('Y',$time);
        $nowmonth = date('n',$time);

        $finder = MemberMonthOrder::where(['member_id'=>$this->order->uid,'year'=>$nowyear,'month'=>$nowmonth])->first();
        if($finder){
            $finder->order_num += 1;
            $finder->order_price = bcadd($finder->order_price ,$this->order->price,2);
            $finder->save();
        }else{
            $data=[];
            $data['member_id'] = $this->order->uid;
            $data['year'] = $nowyear;
            $data['month'] = $nowmonth;
            $data['order_num'] = 1;
            $data['order_price'] = $this->order->price;
            MemberMonthOrder::create($data);
        }

    }
}