<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_id')->constrained('school_discounts')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('admission_students')->onDelete('cascade');

            $table->decimal('before_amount', 12, 2)->nullable();
            $table->decimal('after_amount', 12, 2)->nullable();

            $table->enum('status', ['active', 'cancelled', 'expired'])->default('active');
            $table->string('cancel_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();

            $table->unique(['discount_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_students');
    }
};
