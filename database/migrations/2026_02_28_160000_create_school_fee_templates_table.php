<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_fee_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained('school_groups')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('school_sections')->onDelete('cascade');
            $table->foreignId('session_id')->nullable()->constrained('school_sessions')->onDelete('cascade');

            $table->string('fee_type_name');
            $table->string('fee_name')->nullable();
            $table->unsignedBigInteger('exam_id')->nullable();
            $table->date('pay_date')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->enum('frequency', ['one_time', 'monthly', 'per_exam', 'event_triggered'])->default('one_time');
            $table->tinyInteger('due_day')->unsigned()->nullable()->comment('Day of month for monthly fees (1-31)');

            $table->timestamps();

            $table->foreign('exam_id')
                ->references('id')
                ->on('school_exam_names')
                ->onDelete('set null');

            $table->index(['school_id', 'class_id', 'session_id', 'fee_type_name'], 'idx_fee_templates_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_fee_templates');
    }
};
