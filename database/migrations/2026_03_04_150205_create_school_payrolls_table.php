<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('school_employee_id')->constrained('school_employees')->onDelete('cascade');
            $table->string('employee_name');
            $table->string('mobile_number');
            $table->string('designation');
            $table->string('month');
            $table->integer('year');
            $table->integer('present')->default(0);
            $table->integer('absent')->default(0);
            $table->integer('leave')->default(0);
            $table->decimal('total_payable', 15, 2);
            $table->decimal('payable_due', 15, 2)->default(0);
            $table->enum('advance_status', ['Yes', 'No'])->default('No');
            $table->string('pay_type'); // e.g., Cash, Bank, Mobile
            $table->decimal('paid_amount', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_payrolls');
    }
};