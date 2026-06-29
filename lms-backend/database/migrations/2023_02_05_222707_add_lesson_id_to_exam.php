<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLessonIdToExam extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->uuid('course_id')->nullable()->change();
            $table->foreign('course_id')->references('id')->on('courses');
            $table->foreignUuid('lesson_id')->nullable()->references('id')->on('lessons');
            $table->boolean('is_quiz')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exams', function (Blueprint $table) {
            //
        });
    }
}
