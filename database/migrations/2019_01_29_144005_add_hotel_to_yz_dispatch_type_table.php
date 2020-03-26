<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHotelToYzDispatchTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_dispatch_type')) {

            if (!\app\common\models\DispatchType::where('name','酒店入住')->count()) {
                \app\common\models\DispatchType::where('id', 4)->delete();
                \Illuminate\Support\Facades\DB::insert('INSERT INTO `'.app('db')->getTablePrefix().'yz_dispatch_type` (`id`, `name`, `plugin`, `need_send`)
VALUES
	(4, \'酒店入住\', 33, 1);
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
