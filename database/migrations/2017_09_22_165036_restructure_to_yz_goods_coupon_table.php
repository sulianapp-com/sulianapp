<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RestructureToYzGoodsCouponTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_goods_coupon')) {


            if (\Schema::hasTable('yz_goods_coupon')) {

                if (\Schema::hasColumn('yz_goods_coupon', 'is_coupon')) {
                    \Schema::table('yz_goods_coupon',function ($table) {
                        $table->renameColumn('is_coupon','is_give');
                    });
                }
                if (\Schema::hasColumn('yz_goods_coupon', 'coupon_id')) {
                    \Schema::table('yz_goods_coupon',function ($table) {
                        $table->text('coupon_id')->change();
                    });
                }
                if (\Schema::hasColumn('yz_goods_coupon', 'coupon_id')) {
                    \Schema::table('yz_goods_coupon',function ($table) {
                        $table->renameColumn('coupon_id','coupon');
                    });
                }
                if (\Schema::hasColumn('yz_goods_coupon', 'send_times')) {
                    \Schema::table('yz_goods_coupon',function ($table) {
                        $table->renameColumn('send_times','send_type');
                    });
                }


                DB::transaction(function() {
                    if (\Schema::hasColumns('yz_goods_coupon',['is_give','coupon','send_type'])) {

                        $list = DB::table('yz_goods_coupon')->get();
                        foreach ($list as $key => $item) {

                            if (is_numeric($item['coupon'])) {
                                $couponModel = DB::table('yz_coupon')->where('id', $item['coupon'])->first();
                                $coupon = json_encode([['coupon_id' => $item['coupon'], 'coupon_name' => $couponModel['name'], 'coupon_several' => 1]]);

                                DB::table('yz_goods_coupon')->where('id', $item['id'])->update(['coupon' => $coupon]);
                            }
                        }
                    }
                });
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
        //
    }
}
