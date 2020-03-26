<?php

namespace app\Console\Commands;

use app\common\models\Income;
use app\common\models\Withdraw;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class RepairWithdraw extends Command
{

    protected $signature = 'fix:income';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '修复收入';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
       /* $bar = $this->output->createProgressBar(100);

        for($i = 0; $i<100;$i++) {
            $this->info('test success'.$i.'!');
            sleep(1);
            $bar->advance();
        }
        $bar->finish();
        $this->error('test fail!');
        $this->comment('test comment!');*/
        $member_ids = Income::select('member_id')->groupBy('member_id')->get();

        $i = 0;
        $ids = [];
        foreach ($member_ids as $member) {
            //dd($member_id['member_id']);
            $result = $this->withdrawCheck($member['member_id']);
            if (!$result) {
                $ids[] = $member['member_id'];
                $i ++;
            }
        }
        //dump($ids);
        //dd('一共' .$i. '个会员数据存在问题');

        Log::info('一共' .$i. '个会员数据存在问题');

    }


    public function withdrawCheck($member_id)
    {
        //$member_id = 2450;


        $withdraws = Withdraw::select('type_id')->where('member_id',$member_id)->get();


        $withdraw_ids = '';
        foreach ($withdraws as $key => $item) {
            $withdraw_ids =  $item['type_id'] . ',' . $withdraw_ids;
        }
        $withdraw_ids = explode(',', $withdraw_ids, -1);


        $incomes = Income::select('id')->where('member_id',$member_id)->where('status',1)->get();

        $income_ids = [];
        foreach ($incomes as $key => $item) {
            $income_ids[] =$item['id'];
        }


        /*dump($withdraw_ids);

        dump($income_ids);

        dump(array_diff($withdraw_ids,$income_ids));
        dd(array_diff($income_ids,$withdraw_ids));*/


        $array = array_diff($income_ids,$withdraw_ids);
        if (empty($array)) {
            return true;
        }
        Income::where('member_id',$member_id)->whereIn('id',$array)->update(['status' => 0, 'pay_status' => 0]);
        Log::info('会员ID:' .$member_id. '收入提现数据错误修复成功');

        //dump('会员ID:' .$member_id. '收入提现数据错误修复成功');
        return false;

    }

}
