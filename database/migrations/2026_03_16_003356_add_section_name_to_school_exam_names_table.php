<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('school_exam_names', function (Blueprint $table) {
            // Adding section_name after class_name for better schema organization
            $table->string('section_name')->nullable()->after('class_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_exam_names', function (Blueprint $table) {
            $table->dropColumn('section_name');
        });
    }
};