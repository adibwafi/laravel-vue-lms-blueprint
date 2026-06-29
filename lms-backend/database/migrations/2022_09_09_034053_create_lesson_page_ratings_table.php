<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonPageRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_page_ratings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('lesson_pages_id');
            $table->string('users_id');
            $table->integer('rating');
            $table->text('feedback');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lesson_page_ratings');
    }
}
