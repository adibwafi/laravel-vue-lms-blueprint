<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnCourses extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('courses', function (Blueprint $table) {
      $table->string('price')->nullable()->default('0')->change();
      $table->string('link_payment')->nullable()->default('#')->change();
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
