<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admission_students', function (Blueprint $table) {
            // Make interview_code nullable
            $table->string('interview_code')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('admission_students', function (Blueprint $table) {
            // Revert back to NOT NULL
            $table->string('interview_code')->nullable(false)->change();
        });
    }
};
