<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string("token",10)->unique();
            $table->boolean('is_redeemed')->default(false);
            $table->timestamp('redeemed_date')->nullable();
            $table->foreignUuid('course_category_id')->constrained();
            $table->foreignUuid('redeemed_by')->nullable()->references('id')->on('users');
            $table->foreignUuid('created_by')->references('id')->on('users');
            $table->softDeletesTz();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voucher');
    }
}
