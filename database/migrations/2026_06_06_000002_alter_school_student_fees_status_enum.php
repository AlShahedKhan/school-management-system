<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE school_student_fees 
            MODIFY COLUMN status ENUM('pending','paid','partial_paid','due','due_partial','over_due','over_due_partial','advance','advance_partial') 
            NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE school_student_fees 
            MODIFY COLUMN status ENUM('pending','partial','paid','overdue','due') 
            NOT NULL DEFAULT 'pending'");
    }
};
