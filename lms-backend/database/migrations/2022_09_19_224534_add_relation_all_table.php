<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationAllTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_reset_passwords', function (Blueprint $table) {
            $table->foreign('users_id')->references('id')->on('users');
        });
        Schema::table('courses', function (Blueprint $table) {
            $table->foreign('categories_id')->references('id')->on('course_categories');
            $table->foreign('tutors_id')->references('id')->on('tutors');
        });
        Schema::table('lessons', function (Blueprint $table) {
            $table->foreign('courses_id')->references('id')->on('courses');
        });
        Schema::table('user_verifications', function (Blueprint $table) {
            $table->foreign('users_id')->references('id')->on('users');
        });
        Schema::table('user_lessons', function (Blueprint $table) {
            $table->foreign('courses_id')->references('id')->on('courses');
            $table->foreign('lessons_id')->references('id')->on('lessons');
            $table->foreign('users_id')->references('id')->on('users');
        });
        Schema::table('user_courses', function (Blueprint $table) {
            $table->foreign('users_id')->references('id')->on('users');
        });
        Schema::table('lesson_pages', function (Blueprint $table) {
            $table->foreign('lessons_id')->references('id')->on('lessons');
        });
        Schema::table('lesson_page_ratings', function (Blueprint $table) {
            $table->foreign('lesson_pages_id')->references('id')->on('lesson_pages');
            $table->foreign('users_id')->references('id')->on('users');
        });
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
