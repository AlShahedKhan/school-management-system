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
        Schema::table('school_exam_schedules', function (Blueprint $table) {
            $table->string('status')->default('Active');
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_exam_schedules', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'published_at',
                'created_by',
            ]);
        });
    }
};
