<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_translations', function (Blueprint $table) {
            $table->id();
            $table->string('key', 150)->unique();
            $table->string('group', 50)->nullable()->index();
            $table->text('en')->nullable();
            $table->text('bn')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_translations');
    }
};
