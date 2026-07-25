<?php

// Created on 2026-07-07: Migration to support active/inactive status toggle
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('admission_students', function (Blueprint $table) {
            // Modified on 2026-07-07: Change status column type and add tracker fields
            $table->string('status', 50)->default('pending')->change();
            $table->date('inactive_date')->nullable()->after('status');
            $table->text('inactive_reason')->nullable()->after('inactive_date');
            $table->date('active_date')->nullable()->after('inactive_reason');
            $table->unsignedBigInteger('status_updated_by')->nullable()->after('active_date');
        });

        // Modified on 2026-07-07: Migrate all existing 'approved' statuses to 'Active'
        DB::table('admission_students')->where('status', 'approved')->update(['status' => 'Active']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admission_students', function (Blueprint $table) {
            // Modified on 2026-07-07: Drop tracker fields
            $table->dropColumn(['inactive_date', 'inactive_reason', 'active_date', 'status_updated_by']);
        });
    }
};
