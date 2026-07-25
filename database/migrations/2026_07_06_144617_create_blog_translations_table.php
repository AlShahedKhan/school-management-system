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
        Schema::create('blog_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('blog_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('locale', 5);

            $table->string('title');
            $table->longText('description');

            $table->text('seo_tags')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();

            $table->timestamps();

            $table->unique(['blog_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_translations');
    }
};
