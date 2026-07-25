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
        Schema::table('school_syllabuses', function (Blueprint $table) {
            // Adding school_id after the id column
            $table->foreignId('school_id')
                  ->after('id')
                  ->constrained('schools')
                  ->onDelete('cascade');
            
            // Adding an index for performance optimization
            $table->index('school_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_syllabuses', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });
    }
};