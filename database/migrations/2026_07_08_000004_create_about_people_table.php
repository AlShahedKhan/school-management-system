<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_people', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('slug')->unique();
            $table->string('name_en');
            $table->string('name_bn')->nullable();
            $table->string('designation_en');
            $table->string('designation_bn')->nullable();
            $table->text('summary_en');
            $table->text('summary_bn')->nullable();
            $table->longText('details_en');
            $table->longText('details_bn')->nullable();
            $table->unsignedInteger('sort_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_people');
    }
};
