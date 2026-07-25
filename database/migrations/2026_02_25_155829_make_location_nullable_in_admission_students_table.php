<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admission_students', function (Blueprint $table) {
            // Make these columns nullable
            $table->string('division')->nullable()->change();
            $table->string('district')->nullable()->change();
            $table->string('upazila')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('admission_students', function (Blueprint $table) {
            // Revert back to NOT NULL
            $table->string('division')->nullable(false)->change();
            $table->string('district')->nullable(false)->change();
            $table->string('upazila')->nullable(false)->change();
        });
    }
};
