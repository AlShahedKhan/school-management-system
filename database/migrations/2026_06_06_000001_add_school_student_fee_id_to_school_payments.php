<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_payments', function (Blueprint $table) {
            $table->foreignId('school_student_fee_id')
                  ->nullable()
                  ->constrained('school_student_fees')
                  ->nullOnDelete()
                  ->after('school_id');
            $table->index('school_student_fee_id');
        });
    }

    public function down(): void
    {
        Schema::table('school_payments', function (Blueprint $table) {
            $table->dropForeign(['school_student_fee_id']);
            $table->dropIndex(['school_student_fee_id']);
            $table->dropColumn('school_student_fee_id');
        });
    }
};
