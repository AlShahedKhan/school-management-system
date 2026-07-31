<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('school_payments', 'status')) {
            Schema::table('school_payments', function (Blueprint $table) {
                $table->string('status')->default('unpaid')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('school_payments', 'status')) {
            Schema::table('school_payments', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
