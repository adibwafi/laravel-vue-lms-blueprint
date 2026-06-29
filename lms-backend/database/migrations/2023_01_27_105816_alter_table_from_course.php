<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableFromCourse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->text('what_learn')->nullable()->change();
            $table->text('skill')->nullable()->change();
            $table->bigInteger('price')->nullable()->change();
            $table->text('requirements')->nullable()->change();
            $table->boolean('isPaid')->default(false)->change();
            $table->text('prasyarat')->nullable();
            $table->text('metode_absensi')->nullable();
            $table->text('durasi_pelatihan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            //
        });
    }
}
