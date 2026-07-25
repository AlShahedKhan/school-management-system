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
        Schema::create('admin_sms_activations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('sms_type'); // admission, readmission, promotion, teacher_registration, income
            $table->unsignedBigInteger('admin_sms_template_id')->nullable(); // custom template, NULL for default
            
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('schedule_type', ['single', 'daily', 'multiple']);
            $table->json('schedule_dates')->nullable();
            
            $table->enum('send_channel', ['message', 'call', 'both'])->default('message');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('admin_sms_template_id')->references('id')->on('admin_sms_templates')->onDelete('set null');
            $table->unique(['school_id', 'sms_type'], 'school_sms_type_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_sms_activations');
    }
};
