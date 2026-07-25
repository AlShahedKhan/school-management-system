<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');

            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained('school_groups')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('school_sections')->onDelete('cascade');
            $table->foreignId('session_id')->constrained('school_sessions')->onDelete('cascade');

            $table->enum('discount_category', ['exam_waiver', 'specific_months', 'full_session']);
            $table->enum('discount_type', ['Fixed', 'Percentage']);
            $table->decimal('discount_value', 12, 2);

            $table->foreignId('fee_template_id')->nullable()->constrained('school_fee_templates')->onDelete('cascade');

            $table->enum('student_scope', ['selected', 'all'])->default('selected');
            $table->boolean('auto_apply_new_students')->default(false);

            $table->foreignId('exam_id')->nullable()->constrained('school_exam_names')->onDelete('set null');
            $table->decimal('min_gpa', 4, 2)->nullable();
            $table->decimal('min_marks', 5, 2)->nullable();

            $table->json('months')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_discounts');
    }
};
