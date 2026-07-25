<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_payments', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('admission_student_id')->constrained('admission_students')->onDelete('cascade');
            $blueprint->string('fees_type');
            
            // Using default(0) is better than nullable for calculations
            $blueprint->decimal('total_payable', 10, 2)->default(0);
            $blueprint->decimal('payable_due', 10, 2)->default(0);
            $blueprint->decimal('total_amount', 10, 2)->default(0); 
            $blueprint->decimal('total_due', 10, 2)->default(0);
            
            $blueprint->enum('pay_type', ['Due', 'Payable', 'Advance'])->default('Payable');
            $blueprint->decimal('type_amount', 10, 2)->default(0);
            
            $blueprint->date('pay_date');
            $blueprint->string('pay_method')->default('Cash')->nullable(); // Method can be null/default
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_payments');
    }
};