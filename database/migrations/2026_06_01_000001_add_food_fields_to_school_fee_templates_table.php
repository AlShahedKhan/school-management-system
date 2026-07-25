<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_fee_templates', function (Blueprint $table) {
            $table->string('food_type')->nullable()->after('due_day');
            $table->json('student_ids')->nullable()->after('food_type');
        });
    }

    public function down(): void
    {
        Schema::table('school_fee_templates', function (Blueprint $table) {
            $table->dropColumn(['food_type', 'student_ids']);
        });
    }
};
