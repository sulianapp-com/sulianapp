<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUpdatedAtToImsYzMemberUniqueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_member_unique')) {
            Schema::table('yz_member_unique', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_member_unique', 'updated_at')) {

                    $table->integer('updated_at')->default(0);
                }

                if (!Schema::hasColumn('yz_member_unique', 'deleted_at')) {

                    $table->integer('deleted_at')->nullable();
                }

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
        if (Schema::hasTable('yz_member_unique')) {
            Schema::table('yz_member_unique', function (Blueprint $table) {
                $table->dropColumn('updated_at');
                $table->dropColumn('deleted_at');
            });
        }
    }
}
