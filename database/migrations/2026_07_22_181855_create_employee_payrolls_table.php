<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\SalaryTypeEnum;
use App\Enums\PaymentMethodEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_payrolls', function (Blueprint $table) {
            $table->id();
             $table->foreignId('school_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('employee_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->enum(
                    'salary_type',
                    SalaryTypeEnum::values()
                )->default(SalaryTypeEnum::PAID->value);
            $table->decimal('receive_amount', 12, 2);
            $table->unsignedTinyInteger('receive_month');
            $table->unsignedSmallInteger('receive_year');
            $table->date('receive_date');
            $table->enum(
                    'payment_method',
                    PaymentMethodEnum::values()
                )->default(PaymentMethodEnum::CASH->value);
            $table->string('bank_name')->nullable();
            $table->string('transaction_id')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index('employee_id');
            $table->index(['receive_month', 'receive_year']);
            $table->index('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_payrolls');
    }
};
