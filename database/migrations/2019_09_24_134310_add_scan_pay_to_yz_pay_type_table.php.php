<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddScanPayToYzPayTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_pay_type')) {
            \Illuminate\Support\Facades\DB::insert('INSERT INTO `'.app('db')->getTablePrefix().'yz_pay_type` (`id`, `name`, `code`, `setting_key`, `type`, `plugin_id`, `unit`, `updated_at`, `created_at`, `deleted_at`, `need_password`)
            VALUES
                (38, \'微信扫码支付\', \'WechatScan\', \'shop.wechat_set\', 2, 0, \'元\', 1559528246, NULL, NULL, 0),
                (39, \'微信人脸支付\', \'WechatFace\', \'shop.wechat_set\', 2, 0, \'元\', 1559528246, NULL, NULL, 0),
                (40, \'支付宝扫码支付\', \'AlipayScan\', \'shop.alipay_set\', 2, 0, \'元\', 1559528246, NULL, NULL, 0),
                (41, \'支付宝人脸支付\', \'AlipayFace\', \'shop.alipay_set\', 2, 0, \'元\', 1559528246, NULL, NULL, 0)
            ');
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
