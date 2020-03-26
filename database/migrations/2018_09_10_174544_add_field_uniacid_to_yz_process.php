<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldUniacidToYzProcess extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_process')) {
            Schema::table('yz_process', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_process', 'uniacid')) {
                    $table->integer('uniacid')->default(0);
                }
            });
        }

        if (Schema::hasTable('yz_process')) {
            Schema::table('yz_process', function (Blueprint $table) {
                if (Schema::hasColumn('yz_process', 'uniacid')) {
                    //更新 uniacid 值
                    $records = \app\common\models\Process::get();

                    if ($records->isEmpty()) {
                        return true;
                    }
                    foreach ($records as $key => $record) {

                        $memberModel = \app\common\models\Member::whereUid($record->uid)->first();
                        if($memberModel){
                            $record->uniacid = $memberModel->uniacid;
                            $record->save();
                        }
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
