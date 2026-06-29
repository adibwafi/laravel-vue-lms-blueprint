<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyUserLessonAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_lesson_answers', function (Blueprint $table) {
            // Ubah tipe kolom 'answer' dari JSON menjadi string
            $table->string('answer')->change();
            // Tambah kolom 'notes' dengan tipe string
            $table->string('notes')->nullable()->after('answer');
            // Tambah kolom 'admin_id' dengan tipe string
            $table->string('admin_id')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_lesson_answers', function (Blueprint $table) {
            // Kembalikan tipe kolom 'answer' ke JSON
            $table->json('answer')->change();
            // Hapus kolom 'notes'
            $table->dropColumn('notes');
            // Hapus kolom 'admin_id'
            $table->dropColumn('admin_id');
        });
    }
}
