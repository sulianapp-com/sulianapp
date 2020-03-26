<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDataToYzPayType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Schema::hasTable('yz_pay_type')) {
            Schema::table('yz_pay_type', function (Blueprint $table) {
                if (!\Schema::hasColumn('yz_pay_type', 'code')) {
                    $table->string('code', 50);
                }
                if (!\Schema::hasColumn('yz_pay_type', 'type')) {
                    $table->tinyInteger('type');
                }
                if (!\Schema::hasColumn('yz_pay_type', 'plugin_id')) {
                    $table->tinyInteger('plugin_id');
                }
                if (!\Schema::hasColumn('yz_pay_type', 'unit')) {
                    $table->string('unit', 50);
                }

                if (!\Schema::hasColumn('yz_pay_type', 'updated_at')) {

                    $table->integer('updated_at')->nullable();
                }
                if (!\Schema::hasColumn('yz_pay_type', 'created_at')) {

                    $table->integer('created_at')->nullable();
                }
                if (!\Schema::hasColumn('yz_pay_type', 'deleted_at')) {

                    $table->integer('deleted_at')->nullable();
                }

            });
            \Illuminate\Support\Facades\DB::transaction(function () {
                $this->syncData();
            });
        }
    }

    private function syncData()
    {
        \app\common\models\PayType::where('id','>','-1')->delete();
        \app\common\models\PayType::insert([
                ['name'=>'未支付', 'code'=>'unPay', 'type'=>0, 'plugin_id'=>0, 'unit'=>''],
                ['name'=>'微信支付', 'code'=>'wechatPay', 'type'=>2, 'plugin_id'=>0, 'unit'=>'元'],
                ['name'=>'支付宝支付', 'code'=>'alipay', 'type'=>2, 'plugin_id'=>0, 'unit'=>'元'],
                ['name'=>'余额支付', 'code'=>'balance', 'type'=>1, 'plugin_id'=>0, 'unit'=>'元'],
                ['name'=>'金币支付', 'code'=>'gold', 'type'=>1, 'plugin_id'=>0, 'unit'=>'金币']]
        );
        \app\common\models\PayType::where('code','unPay')->update(['id'=>0]);
        \app\common\models\PayType::where('code','wechatPay')->update(['id'=>1]);
        \app\common\models\PayType::where('code','alipay')->update(['id'=>2]);
        \app\common\models\PayType::where('code','balance')->update(['id'=>3]);
        \app\common\models\PayType::where('code','gold')->update(['id'=>4]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (\Schema::hasTable('yz_pay_type')) {
            Schema::table('yz_pay_type', function (Blueprint $table) {
                $table->dropColumn('code');
            });
        }
    }
}
