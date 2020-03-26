<?php

use Illuminate\Support\Facades\Schema;
use \app\common\models\Flow;
use Illuminate\Database\Migrations\Migration;

class AddDataToStatusFlowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_status')) {
            return;
        }
        $this->remittance();
    }

    private function remittance()
    {
        /**
         * @var Flow $flow
         */
        $flow = \app\common\models\Flow::create([
            'name' => '邮局汇款',
            'code' => 'remittance',
        ]);
        $flow->pushManyStatus([
            [
                'code' => 'waitRemittance',
                'name' => '待汇款',
                'order' => 0,

            ], [
                'name' => '待收款',
                'code' => 'waitReceipt',
                'order' => 10,
            ], [
                'name' => '已完成',
                'code' => 'completed',
                'order' => 20,

            ], [
                'name' => '已取消',
                'code' => 'canceled',
                'order' => -1,

            ], [
                'name' => '已关闭',
                'code' => 'closed',
                'order' => -2,

            ],
        ]);
        /**
         * @var Flow $flow
         */
        $flow = \app\common\models\Flow::create([
            'name' => '邮局转账审核',
            'code' => 'remittanceAudit',
        ]);
        $flow->pushManyStatus([
            [
                'code' => 'waitAudit',
                'name' => '待审核',
                'order' => 0,

            ], [
                'name' => '已通过',
                'code' => 'passed',
                'order' => 10,

            ], [
                'name' => '已取消',
                'code' => 'canceled',
                'order' => -1,

            ], [
                'name' => '已拒绝',
                'code' => 'refused',
                'order' => -2,

            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
