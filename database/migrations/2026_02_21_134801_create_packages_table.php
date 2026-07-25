<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();

            $table->enum('package_type', ['Basic', 'Standard', 'Premium', 'Advance']);

            $table->integer('student_limit');
            $table->integer('teacher_limit');

            $table->integer('free_trial_days')->default(0);

            $table->decimal('per_student_price', 10, 2);

            $table->decimal('total_payable', 12, 2);

            $table->decimal('annual_discount_percent', 5, 2)->default(0);

            $table->decimal('after_discount', 12, 2);

            $table->integer('sms_limit')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
