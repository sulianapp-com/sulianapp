<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTokenToYzMemberTable extends Migration
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
                if (!Schema::hasColumn('yz_member', 'access_token_1')) {
                    $table->string('access_token_1', 512)->default(0);
                }

                if (!Schema::hasColumn('yz_member', 'access_expires_in_1')) {
                    $table->integer('access_expires_in_1')->default(0);
                }

                if (!Schema::hasColumn('yz_member', 'refresh_token_1')) {
                    $table->string('refresh_token_1', 512)->default(0);
                }

                if (!Schema::hasColumn('yz_member', 'refresh_expires_in_1')) {
                    $table->integer('refresh_expires_in_1')->default(0);
                }

                if (!Schema::hasColumn('yz_member', 'access_token_2')) {
                    $table->string('access_token_2', 512)->default(0);
                }

                if (!Schema::hasColumn('yz_member', 'access_expires_in_2')) {
                    $table->integer('access_expires_in_2')->default(0);
                }

                if (!Schema::hasColumn('yz_member', 'refresh_token_2')) {
                    $table->string('refresh_token_2', 512)->default(0);
                }

                if (!Schema::hasColumn('yz_member', 'refresh_expires_in_2')) {
                    $table->integer('refresh_expires_in_2')->default(0);

                    $table->index(['access_token_1', 'member_id']);
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
        //
    }
}
