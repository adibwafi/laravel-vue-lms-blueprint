<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_ratings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('lessons_id');
            $table->uuid('users_id');
            $table->integer('rating');
            $table->text('feedback');
            $table->timestamps();
        });

        Schema::table('lesson_ratings', function (Blueprint $table) {
            $table->foreign('lessons_id')->references('id')->on('lessons');
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
        Schema::dropIfExists('lesson_ratings');
    }
}
