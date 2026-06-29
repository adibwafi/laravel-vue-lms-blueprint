<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCourseTutorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_tutor', function (Blueprint $table) {
            $table->string('course_id');
            $table->string('tutor_id');
            $table->foreign('course_id')->references('id')->on('courses');
            $table->foreign('tutor_id')->references('id')->on('tutors');
        });
        //
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
