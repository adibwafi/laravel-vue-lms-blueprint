<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_pages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('lessons_id');
            $table->string('name');
            $table->string('type_content');
            $table->integer('sorter');
            $table->text('content');
            $table->string('media');
            $table->text('media_link');
            $table->text('tooltips');
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
        Schema::dropIfExists('lesson_pages');
    }
}
