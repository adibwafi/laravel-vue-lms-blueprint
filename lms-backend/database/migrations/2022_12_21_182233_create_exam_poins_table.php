<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamPoinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_poins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('exam_id');
            $table->string('user_id');
            $table->decimal('poin');
            $table->dateTime('finished_at')->nullable();
            $table->timestamps();
        });

        Schema::table('exam_poins', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('exam_poins');
    }
}
