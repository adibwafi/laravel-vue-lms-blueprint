<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRedeemCodeAndPrakerjaStateToUserCourseCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_course_categories', function (Blueprint $table) {
            $table->string('redeem_code')->nullable()->after('user_id');
            $table->boolean('prakerja_state')->nullable()->after('redeem_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_course_categories', function (Blueprint $table) {
            $table->dropColumn('redeem_code');
            $table->dropColumn('prakerja_state');
        });
    }
}
