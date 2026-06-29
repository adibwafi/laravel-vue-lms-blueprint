<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_pages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('exam_id');
            $table->string('name');
            $table->integer('order');
            $table->json('data');
            $table->string('media_type');
            $table->text('media_link');
            $table->json('tooltips');
            $table->string('content_type');
            $table->timestamps();
        });

        Schema::table('exam_pages', function (Blueprint $table) {
            $table->foreign('exam_id')->references('id')->on('exams');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exam_pages');
    }
}
