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
        Schema::create('advance_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('student_id');
            $table->decimal('amount', 10, 2);
            $table->decimal('monthly_fee', 10, 2);
            $table->integer('full_months')->default(0);
            $table->decimal('partial_credit', 10, 2)->default(0);
            $table->decimal('remaining_credit', 10, 2);
            $table->date('pay_date');
            $table->string('pay_method', 50)->default('Cash');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('admission_students')->onDelete('cascade');
            $table->index(['school_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advance_payments');
    }
};
