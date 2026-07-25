<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_sections', function (Blueprint $table) {
            $table->dropColumn(['session', 'roll', 'student_id_number', 'student_name', 'total_fees', 'fees_due']);
        });
    }

    public function down(): void
    {
        Schema::table('school_sections', function (Blueprint $table) {
            $table->string('session')->nullable();
            $table->string('roll')->nullable();
            $table->string('student_id_number')->nullable();
            $table->string('student_name')->nullable();
            $table->decimal('total_fees', 12, 2)->nullable()->default(0);
            $table->decimal('fees_due', 12, 2)->nullable()->default(0);
        });
    }
};
