<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsCronJobTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('cron_job')) {
            Schema::create('cron_job', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->text('return', 65535);
                $table->float('runtime');
                $table->integer('cron_manager_id')->unsigned();
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
        Schema::drop('ims_cron_job');
    }

}
