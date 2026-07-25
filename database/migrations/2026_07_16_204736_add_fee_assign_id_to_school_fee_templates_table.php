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
        Schema::table('school_fee_templates', function (Blueprint $table) {
            $table->foreignId('fee_assign_id')->nullable()->after('session_id')->constrained('school_fee_assigns')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_fee_templates', function (Blueprint $table) {
            $table->dropForeign(['fee_assign_id']);
            $table->dropColumn('fee_assign_id');
        });
    }
};
