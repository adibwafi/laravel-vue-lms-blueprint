<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultToLessonsPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lesson_pages', function (Blueprint $table) {
            $table->dropColumn('media');
            $table->dropColumn('type_content');
            $table->dropColumn('sorter');
            $table->string('media_type');
            $table->string('content_type');
            $table->integer('order');
            $table->string('media_link')->change()->nullable();
            $table->string('tooltips')->change()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lessons_pages', function (Blueprint $table) {
            //
        });
    }
}
