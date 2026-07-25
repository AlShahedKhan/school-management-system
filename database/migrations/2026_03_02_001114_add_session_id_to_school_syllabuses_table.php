<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_syllabuses', function (Blueprint $table) {
            // Adding the column after ID
            $table->unsignedBigInteger('session_id')->nullable()->after('id');
            
            // Defining the Foreign Key Relationship
            $table->foreign('session_id')
                  ->references('id')
                  ->on('school_sessions')
                  ->onDelete('cascade'); 
        });
    }

    public function down(): void
    {
        Schema::table('school_syllabuses', function (Blueprint $table) {
            // Drop foreign key first, then the column
            $table->dropForeign(['session_id']);
            $table->dropColumn('session_id');
        });
    }
};