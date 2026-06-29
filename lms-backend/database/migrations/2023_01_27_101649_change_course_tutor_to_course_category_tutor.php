<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeCourseTutorToCourseCategoryTutor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop("course_tutor");

        Schema::create('course_category_tutor', function (Blueprint $table) {
            $table->string('course_category_id');
            $table->string('tutor_id');
            $table->foreign('course_category_id')->references('id')->on('course_categories')
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
        Schema::create('course_tutor', function (Blueprint $table) {
            $table->string('course_category_id');
            $table->string('tutor_id');
            $table->foreign('course_category_id')->references('id')->on('course_categories')
                ->onDelete('cascade');
            $table->foreign('tutor_id')->references('id')->on('tutors')
                ->onDelete('cascade');
        });

        Schema::drop("course_category_tutor");
    }
}
