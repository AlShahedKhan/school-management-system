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
        Schema::table('school_payments', function (Blueprint $table) {
            // 1. Add the school_id column after the ID
            $table->unsignedBigInteger('school_id')->after('id')->nullable();

            // 2. Setup Foreign Key (Optional but recommended for SaaS)
            $table->foreign('school_id')
                  ->references('id')
                  ->on('schools')
                  ->onDelete('cascade');

            // 3. Add index for performance in the Due List ledger
            $table->index('school_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_payments', function (Blueprint $table) {
            // Drop foreign key first, then the column
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });
    }
};