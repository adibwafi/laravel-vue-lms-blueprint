<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeExamAnswerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exam_answers', function (Blueprint $table) {
            $table->dropConstrainedForeignId("exam_id");
            $table->foreignUuid("exam_poin_id")->references("id")->on("exam_poins");
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exam_answers', function (Blueprint $table) {
        });
    }
}
