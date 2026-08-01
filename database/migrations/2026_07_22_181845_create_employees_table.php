<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\EmployeeStatusEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
             $table->foreignId('school_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('employee_no')->unique();
            $table->string('name');
            $table->string('mobile_number', 20);
            $table->string('designation');
            $table->decimal('salary_amount', 12, 2);
            $table->date('salary_start_date');
            $table->unsignedTinyInteger('pay_date')->default(10);
            $table->enum(
                    'employee_status',
                    EmployeeStatusEnum::values()
                )->default(EmployeeStatusEnum::ACTIVE->value);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index('employee_no');
            $table->index('mobile_number');
            $table->index('designation');
            $table->index('employee_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
