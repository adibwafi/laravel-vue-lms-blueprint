<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnFromCourseCategories extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('course_categories', function (Blueprint $table) {
      $table->text('what_learn')->nullable()->change();
      $table->string('skill')->nullable()->change();
      $table->string('price')->nullable()->change();
    });

    Schema::create('course_tutor', function (Blueprint $table) {
      $table->string('course_id');
      $table->string('tutor_id');
      $table->foreign('course_id')->references('id')->on('course_categories')
        ->onDelete('cascade');
      $table->foreign('tutor_id')->references('id')->on('tutors')
        ->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('course_categories', function (Blueprint $table) {
      //
    });
  }
}
