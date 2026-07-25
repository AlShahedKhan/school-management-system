<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_student_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('admission_students')->onDelete('cascade');
            $table->foreignId('fee_template_id')->nullable()->constrained('school_fee_templates')->onDelete('set null');

            $table->string('fee_type_name');
            $table->string('fee_name')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->date('pay_date')->nullable();
            $table->enum('status', ['pending', 'partial', 'paid', 'overdue'])->default('pending');

            $table->timestamps();

            $table->index(['school_id', 'student_id', 'fee_type_name'], 'idx_student_fees_lookup');
            $table->index(['school_id', 'fee_type_name', 'fee_name'], 'idx_student_fees_by_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_student_fees');
    }
};
