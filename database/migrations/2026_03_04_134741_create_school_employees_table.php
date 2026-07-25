<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_employees', function (Blueprint $attribute) {
            $attribute->id();
            $attribute->foreignId('school_id')->constrained()->onDelete('cascade');
            $attribute->string('employee_name');
            $attribute->string('mobile_number');
            $attribute->string('designation');
            $attribute->decimal('monthly_fee', 10, 2)->default(0); // Assuming this is deduction/fee
            $attribute->decimal('salary_amount', 15, 2);
            $attribute->date('payroll_date');
            $attribute->string('bank_name')->nullable();
            $attribute->string('branch')->nullable();
            $attribute->string('routing_number')->nullable();
            $attribute->string('ac_holder_name')->nullable();
            $attribute->string('ac_number')->nullable();
            $attribute->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_employees');
    }
};