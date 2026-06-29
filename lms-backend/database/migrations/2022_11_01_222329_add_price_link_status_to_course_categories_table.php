<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceLinkStatusToCourseCategoriesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('course_categories', function (Blueprint $table) {
      $table->integer('price')->after('color');
      $table->text('link_payment')->after('price');
      $table->string('status')->after('link_payment')->default('draft');
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
