<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MakeCourseAndSectionNullableInAssignedTeachers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assigned_teachers', function (Blueprint $table) {
            $table->unsignedInteger('course_id')->nullable()->change();
            $table->unsignedInteger('section_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assigned_teachers', function (Blueprint $table) {
            $table->unsignedInteger('course_id')->nullable(false)->change();
            $table->unsignedInteger('section_id')->nullable(false)->change();
        });
    }
}
