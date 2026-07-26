<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Rename legacy column names to foreign key standards in admission_students table.
     */
    public function up(): void
    {
        Schema::table('admission_students', function (Blueprint $table) {
            $table->renameColumn('class', 'class_id');
            $table->renameColumn('section', 'section_id');
            $table->renameColumn('session', 'session_id');
            $table->renameColumn('group', 'group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admission_students', function (Blueprint $table) {
            $table->renameColumn('class_id', 'class');
            $table->renameColumn('section_id', 'section');
            $table->renameColumn('session_id', 'session');
            $table->renameColumn('group_id', 'group');
        });
    }
};
