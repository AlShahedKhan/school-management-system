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
        Schema::create('admin_sms_template_school', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_sms_template_id');
            $table->unsignedBigInteger('school_id');
            $table->timestamps();

            $table->foreign('admin_sms_template_id')->references('id')->on('admin_sms_templates')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_sms_template_school');
    }
};
