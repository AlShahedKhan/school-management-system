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
        Schema::table('employee_payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_payrolls', 'type')) {
                $table->enum('type', ['employee', 'teacher'])->default('employee')->after('school_id');
            }
            if (!Schema::hasColumn('employee_payrolls', 'teacher_id')) {
                $table->foreignId('teacher_id')
                    ->nullable()
                    ->after('employee_id')
                    ->constrained('teachers')
                    ->nullOnDelete();
            }
            // Make employee_id nullable to allow teacher-only payrolls
            $table->foreignId('employee_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_payrolls', function (Blueprint $table) {
            if (Schema::hasColumn('employee_payrolls', 'teacher_id')) {
                $table->dropForeign(['teacher_id']);
                $table->dropColumn('teacher_id');
            }
            if (Schema::hasColumn('employee_payrolls', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
