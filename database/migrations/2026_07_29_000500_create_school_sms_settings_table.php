<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_sms_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('sms_type');
            $table->text('template_body')->nullable();
            $table->string('status')->default('Active');
            $table->timestamps();

            $table->unique(['school_id', 'sms_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_sms_settings');
    }
};
