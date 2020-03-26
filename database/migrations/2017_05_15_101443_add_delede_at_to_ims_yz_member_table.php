<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeledeAtToImsYzMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_member')) {
            Schema::table('yz_member', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_member', 'relation')) {
                    $table->string('relation', 255)->nullable();
                }

                if (!Schema::hasColumn('yz_member', 'created_at')) {
                    $table->integer('created_at')->default(0);
                }

                if (!Schema::hasColumn('yz_member', 'updated_at')) {
                    $table->integer('updated_at')->default(0);
                }

                if (!Schema::hasColumn('yz_member', 'deleted_at')) {
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
        if (Schema::hasTable('yz_member')) {
            Schema::table('yz_member', function (Blueprint $table) {
                $table->dropColumn('relation');
                $table->dropColumn('created_at');
                $table->dropColumn('updated_at');
                $table->dropColumn('deleted_at');
            });
        }
    }
}
