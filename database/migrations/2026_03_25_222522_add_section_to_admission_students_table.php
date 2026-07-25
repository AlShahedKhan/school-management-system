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
        Schema::table('admission_students', function (Blueprint $blueprint) {
            // Adding the section field after the group field
            $blueprint->string('section')->nullable()->after('group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admission_students', function (Blueprint $blueprint) {
            $blueprint->dropColumn('section');
        });
    }
};
