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
        Schema::create('dynamic_operations', function (Blueprint $table) {
            $table->id();
            $table->string('brand_title');
            $table->text('brand_description')->nullable();
            $table->text('promotion_text')->nullable();
            $table->string('brand_logo')->nullable();
            $table->string('school_dashboard_logo')->nullable();
            $table->string('brand_banner')->nullable();
            $table->json('school_dashboard_banners')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dynamic_operations');
    }
};
