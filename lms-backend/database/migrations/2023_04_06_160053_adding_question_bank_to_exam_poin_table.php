<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddingQuestionBankToExamPoinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exam_poins', function (Blueprint $table) {
            $table->foreignUuid('question_bank_id')->nullable(true)->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exam_poins', function (Blueprint $table) {
            $table->dropForeign(['question_bank_id']);
            $table->dropColumn('question_bank_id');
        });
    }
}
