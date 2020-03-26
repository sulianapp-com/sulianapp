<?php

use app\common\models\UniAccount;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveThirdLevelV2 extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_setting')) {
            $uniAccount = UniAccount::getEnable();
            foreach ($uniAccount as $u) {
                \YunShop::app()->uniacid = $u->uniacid;
                \Setting::$uniqueAccountId = $u->uniacid;
                $info = \Setting::get('relation_base');
                if ($info) {
                    if ($info['relation_level'][2]) {
                        unset($info['relation_level'][2]);
                    }
                    if ($info['relation_level']['name3']) {
                        unset($info['relation_level']['name3']);
                    }
                    $request = Setting::set('relation_base',$info);
                }
            }
        }
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
