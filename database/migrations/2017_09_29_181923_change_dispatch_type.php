<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDispatchType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_dispatch_type')) {

            if (!\Schema::hasColumn('yz_dispatch_type', 'need_send')) {
                \Schema::table('yz_dispatch_type', function ($table) {
                    $table->tinyInteger('need_send')->unsigned()->default(0);
                });
            }
            if (!\app\common\models\DispatchType::where('name','门店自提')->count()) {
                \app\common\models\DispatchType::where('id','>','-1')->delete();
                \Illuminate\Support\Facades\DB::insert('INSERT INTO `'.app('db')->getTablePrefix().'yz_dispatch_type` (`id`, `name`, `plugin`, `need_send`)
VALUES
	(1, \'快递\', 0, 1),
	(2, \'门店自提\', 0, 0),
	(3, \'门店配送\', 0, 1);
');
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
