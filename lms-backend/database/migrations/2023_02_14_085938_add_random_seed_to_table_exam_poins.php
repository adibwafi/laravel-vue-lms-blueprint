<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AddRandomSeedToTableExamPoins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exam_poins', function (Blueprint $table) {
            $table->string('random_seed');
        });

        DB::table('exam_poins')->get()->each(function ($exam) {
            DB::table('exam_poins')->where('id', $exam->id)->update(['random_seed' => Str::random(64)]);
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
            $table->dropColumn('random_seed');
        });
    }
}
