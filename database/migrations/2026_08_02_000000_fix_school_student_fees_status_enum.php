<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Unify school_student_fees.status to the `_partial` naming convention.
 *
 * The committed schema (2026_06_06_000002) and the unified
 * FeeStatusSyncService write `due_partial` / `over_due_partial` /
 * `advance_partial`, and every reader queries those names. The live
 * database column was altered out-of-band to `partial_due` /
 * `partial_over_due` / `partial_advance`, which causes
 * "Data truncated for column 'status'" whenever the service writes the
 * `_partial` variants. This migration restores the column to the values
 * the code actually reads and writes.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE school_student_fees
            MODIFY COLUMN status ENUM('unpaid','pending','paid','partial_paid','due','due_partial','over_due','over_due_partial','advance','advance_partial')
            NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE school_student_fees
            MODIFY COLUMN status ENUM('pending','paid','partial_paid','due','due_partial','over_due','over_due_partial','advance','advance_partial')
            NOT NULL DEFAULT 'pending'");
    }
};
