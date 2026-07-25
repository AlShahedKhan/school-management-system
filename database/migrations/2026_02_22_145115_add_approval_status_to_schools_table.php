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
        Schema::table('schools', function (Blueprint $table) {

            // Approval Status
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])
                  ->default('pending')
                  ->after('logo');

            // Optional: Admin approval timestamp
            $table->timestamp('approved_at')->nullable()->after('approval_status');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {

            $table->dropColumn('approval_status');
            $table->dropColumn('approved_at');

        });
    }
};
