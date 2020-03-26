<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzVirtualCoinTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_virtual_coin')) {

            Schema::create('yz_virtual_coin', function (Blueprint $table) {
                $table->increments('id')->comment('虚拟币');
                $table->string('name', 50)->default('')->comment('名称');
                $table->string('code', 50)->default('');
                $table->decimal('exchange_rate', 10)->default(1.00)->comment('汇率');
            });
        }
        if (Schema::hasTable('yz_virtual_coin')) {

            \Illuminate\Support\Facades\DB::update('TRUNCATE TABLE `' . app('db')->getTablePrefix() . 'yz_virtual_coin`');
            \Illuminate\Support\Facades\DB::insert('INSERT INTO `' . app('db')->getTablePrefix() . 'yz_virtual_coin' . '` (`id`, `name`, `code`, `exchange_rate`)
VALUES
	(1, \'爱心值\', \'love\', 1.00),
	(2, \'积分\', \'point\', 1.00),
	(3, \'华侨币\', \'coin\', 1.00);

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
        if (Schema::hasTable('yz_virtual_coin')) {

            Schema::drop('yz_virtual_coin');
        }
    }

}
