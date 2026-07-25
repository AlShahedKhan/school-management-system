<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_page_settings', function (Blueprint $table) {
            $table->id();
            $table->string('page_eyebrow_en')->nullable();
            $table->string('page_eyebrow_bn')->nullable();
            $table->string('page_title_en')->nullable();
            $table->string('page_title_bn')->nullable();
            $table->text('page_intro_en')->nullable();
            $table->text('page_intro_bn')->nullable();
            $table->string('mission_title_en')->nullable();
            $table->string('mission_title_bn')->nullable();
            $table->text('mission_summary_en')->nullable();
            $table->text('mission_summary_bn')->nullable();
            $table->longText('mission_details_en')->nullable();
            $table->longText('mission_details_bn')->nullable();
            $table->string('vision_title_en')->nullable();
            $table->string('vision_title_bn')->nullable();
            $table->text('vision_summary_en')->nullable();
            $table->text('vision_summary_bn')->nullable();
            $table->longText('vision_details_en')->nullable();
            $table->longText('vision_details_bn')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_page_settings');
    }
};
