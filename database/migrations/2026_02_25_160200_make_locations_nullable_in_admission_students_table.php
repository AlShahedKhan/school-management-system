<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admission_students', function (Blueprint $table) {
            $table->string('division')->nullable()->change();
            $table->string('district')->nullable()->change();
            $table->string('upazila')->nullable()->change();

            $table->string('current_division')->nullable()->change();
            $table->string('current_district')->nullable()->change();
            $table->string('current_upazila')->nullable()->change();
            $table->string('current_village')->nullable()->change();

            $table->string('permanent_division')->nullable()->change();
            $table->string('permanent_district')->nullable()->change();
            $table->string('permanent_upazila')->nullable()->change();
            $table->string('permanent_village')->nullable()->change();

            $table->string('interview_code')->nullable()->change(); // if not done yet
        });
    }

    public function down(): void
    {
        Schema::table('admission_students', function (Blueprint $table) {
            $table->string('division')->nullable(false)->change();
            $table->string('district')->nullable(false)->change();
            $table->string('upazila')->nullable(false)->change();

            $table->string('current_division')->nullable(false)->change();
            $table->string('current_district')->nullable(false)->change();
            $table->string('current_upazila')->nullable(false)->change();
            $table->string('current_village')->nullable(false)->change();

            $table->string('permanent_division')->nullable(false)->change();
            $table->string('permanent_district')->nullable(false)->change();
            $table->string('permanent_upazila')->nullable(false)->change();
            $table->string('permanent_village')->nullable(false)->change();

            $table->string('interview_code')->nullable(false)->change();
        });
    }
};
