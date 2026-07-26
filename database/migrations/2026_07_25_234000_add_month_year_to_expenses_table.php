<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'month')) {
                $table->string('month')->nullable()->after('expense_reason');
            }
            if (!Schema::hasColumn('expenses', 'year')) {
                $table->string('year')->nullable()->after('month');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['month', 'year']);
        });
    }
};
