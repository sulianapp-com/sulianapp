<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemakePayType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_pay_type')) {
            \Illuminate\Support\Facades\DB::update('TRUNCATE TABLE `'.app('db')->getTablePrefix().'yz_pay_type`');
            Schema::table('yz_pay_type',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_pay_type', 'setting_key')) {
                        $table->string('setting_key')->nullable();
                    }
                    if (!Schema::hasColumn('yz_pay_type', 'need_password')) {
                        $table->integer('need_password')->default(0);
                    }
                });
        }
        \Illuminate\Support\Facades\DB::insert('INSERT INTO `'.app('db')->getTablePrefix().'yz_pay_type` (`id`, `name`, `code`, `setting_key`, `type`, `plugin_id`, `unit`, `updated_at`, `created_at`, `deleted_at`, `need_password`)
VALUES
	(1, \'微信\', \'wechatPay\', \'shop.pay.weixin\', 2, 0, \'元\', 1505785687, NULL, NULL, 0),
	(2, \'支付宝\', \'alipay\', \'shop.pay.alipay\', 2, 0, \'元\', 1505785687, NULL, NULL, 0),
	(3, \'余额\', \'balance\', \'shop.pay.credit\', 1, 0, \'元\', 1505785687, NULL, NULL, 1),
	(4, \'金币\', \'gold\', \'\', 1, 0, \'金币\', 1505785687, NULL, NULL, 0),
	(5, \'后台\', \'backend\', \'\', 1, 0, \'元\', 1505785688, NULL, NULL, 0),
	(6, \'微信\', \'cloudPayWechat\', \'plugin.cloud_pay_set\', 2, 0, \'元\', 1505785688, NULL, NULL, 0),
	(7, \'支付宝\', \'cloudPayAlipay\', \'\', 2, 0, \'元\', 1505785688, NULL, NULL, 0),
	(8, \'现金\', \'cashPay\', \'\', 1, 0, \'元\', 1505785688, NULL, NULL, 0),
	(9, \'微信\', \'wechatApp\', \'shop_app.pay.weixin\', 2, 0, \'元\', 1505785688, NULL, NULL, 0),
	(10, \'支付宝\', \'alipayApp\', \'shop_app.pay.alipay\', 2, 0, \'元\', NULL, NULL, NULL, 0),
	(11, \'门店\', \'store\', \'\', 1, 0, \'元\', NULL, NULL, NULL, 0),
	(0, \'未支付\', \'unPay\', \'\', 0, 0, \'\', 1505785687, NULL, NULL, 0);

');
        \Illuminate\Support\Facades\DB::update('UPDATE `'.app('db')->getTablePrefix().'yz_pay_type` SET `id`=0 WHERE `code` = \'unPay\'');

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
