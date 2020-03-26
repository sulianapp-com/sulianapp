<?php

use Illuminate\Support\Facades\Schema;
use \app\common\models\Flow;
use Illuminate\Database\Migrations\Migration;

class AddRemittanceAuditToStatusFlowTable extends Migration
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
        $this->audit();
    }

    private function audit()
    {
        /**
         * @var Flow $flow
         */
        $flow = \app\common\models\Flow::create([
            'name' => '转账审核',
            'code' => 'remittanceAudit',
        ]);
        $flow->pushManyStatus([
                [
                    'name' => '待审核',
                    'code' => 'waitAudit',
                    'order' => 0
                ],
                [
                    'name' => '已通过',
                    'code' => 'passed',
                    'order' => 1
                ],
                [
                    'name' => '已取消',
                    'code' => 'canceled',
                    'order' => -1
                ],
                [
                    'name' => '已拒绝',
                    'code' => 'refused',
                    'order' => -2
                ],
            ]
        );
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
