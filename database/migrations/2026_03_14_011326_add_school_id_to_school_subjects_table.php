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
        Schema::table('school_subjects', function (Blueprint $column) {
            // Adding school_id after the id column
            // We use after('id') to keep the table structure clean
            $column->foreignId('school_id')
                   ->after('id')
                   ->constrained('schools')
                   ->onDelete('cascade');
            
            // Adding an index for faster querying by school
            $column->index('school_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_subjects', function (Blueprint $column) {
            // Drop foreign key first, then the column
            $column->dropForeign(['school_id']);
            $column->dropColumn('school_id');
        });
    }
};