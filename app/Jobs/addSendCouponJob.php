<?php

namespace app\Jobs;

use app\common\models\MemberCoupon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class addSendCouponJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $couponData;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($couponData)
    {
        $this->couponData = $couponData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        MemberCoupon::insert($this->couponData);
    }
}
