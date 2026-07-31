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
         Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->morphs('attendable');
            $table->foreignId('device_id')->nullable()->constrained('devices')->onDelete('set null');
            $table->timestamp('timestamp');
            $table->string('status')->default('present');
            $table->string('type')->nullable();
            $table->string('source')->default('device');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
