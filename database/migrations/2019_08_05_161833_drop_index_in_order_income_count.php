<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropIndexInOrderIncomeCount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_order_income_count')) {
            Schema::table('yz_order_income_count', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $preFix = Schema::getConnection()->getTablePrefix();
                $indexesFound = $sm->listTableIndexes($preFix . 'yz_order_income_count');

                if (array_key_exists("idx_order", $indexesFound)) {
                    $table->dropIndex('idx_order');
                }
            });
        }
        //重启队列
        $supervisor = app('supervisor');
        $supervisor->setTimeout(5000);  // microseconds
        $supervisor->restart();
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

    public function hasIndex($table, $name)
    {
        $conn = Schema::getConnection();
        $dbSchemaManager = $conn->getDoctrineSchemaManager();
        $doctrineTable = $dbSchemaManager->listTableDetails($table);
        return $doctrineTable->hasIndex($name);
    }


}
