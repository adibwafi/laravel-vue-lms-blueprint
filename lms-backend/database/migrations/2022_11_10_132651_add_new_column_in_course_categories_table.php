<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnInCourseCategoriesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('course_categories', function (Blueprint $table) {
      $table->dropColumn('status');
    });
    Schema::table('course_categories', function (Blueprint $table) {
      $table->enum('status', ['draft', 'released', 'disabled'])->default('draft');
      $table->string('banner');
      $table->string('description');
      $table->string('what_learn');
      $table->string('skill');
      $table->string('requirements')->nullable();
      $table->string('tutors_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('course_category', function (Blueprint $table) {
      //
    });
  }
}
