<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_subjects', function (Blueprint $blueprint) {
            // This changes the column to allow NULL values
            $blueprint->string('subject_type')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('school_subjects', function (Blueprint $blueprint) {
            // This reverts it back to required if you ever roll back
            $blueprint->string('subject_type')->nullable(false)->change();
        });
    }
};