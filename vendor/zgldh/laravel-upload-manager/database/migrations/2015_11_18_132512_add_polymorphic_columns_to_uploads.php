<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPolymorphicColumnsToUploads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uploads', function (Blueprint $table) {
            $table->integer('uploadable_id')->nullable();
            $table->string('uploadable_type')->nullable();

            $table->index([
                'uploadable_id',
                'uploadable_type'
            ],'uploadable_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uploads', function (Blueprint $table) {
            $table->dropIndex('uploadable_index');
        });
        Schema::table('uploads', function (Blueprint $table) {
            $table->dropColumn('uploadable_id');
            $table->dropColumn('uploadable_type');
        });
    }
}
