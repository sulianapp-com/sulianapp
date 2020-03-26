<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJsapiPayToYzPayTypeTable extends Migration
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
                (48, \'微信支付(服务商)\', \'WechatJsapi\', \'shop.wechat_set\', 2, 0, \'元\', 1559528246, NULL, NULL, 0)
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
