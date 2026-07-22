<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLessonAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_lesson_answers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('courses_id');
            $table->uuid('lessons_id');
            $table->uuid('users_id');
            $table->uuid('lesson_pages_id');
            $table->string('answer')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('user_lesson_answers', function (Blueprint $table) {
            $table->foreign('courses_id')->references('id')->on('courses');
            $table->foreign('lessons_id')->references('id')->on('lessons');
            $table->foreign('users_id')->references('id')->on('users');
            $table->foreign('lesson_pages_id')->references('id')->on('lesson_pages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_lesson_answers');
    }
}
