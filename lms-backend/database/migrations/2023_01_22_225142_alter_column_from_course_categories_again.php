<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnFromCourseCategoriesAgain extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::dropIfExists('course_tutor');

    Schema::create('course_tutor', function (Blueprint $table) {
      $table->uuid('course_categories_id');
      $table->uuid('tutors_id');
      $table->foreign('course_categories_id')->references('id')->on('course_categories')
        ->onDelete('cascade');
      $table->foreign('tutors_id')->references('id')->on('tutors')
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
