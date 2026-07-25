<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Added on 2026-07-09: Add dynamic admission ID tracking and backfill existing data
     */
    public function up(): void
    {
        Schema::table('admission_students', function (Blueprint $table) {
            $table->string('admission_id')->nullable()->after('student_id_number');
        });

        Schema::table('student_promotions', function (Blueprint $table) {
            $table->string('from_admission_id')->nullable()->after('from_student_id_number');
            $table->string('to_admission_id')->nullable()->after('to_student_id_number');
        });

        // Backfill existing data where admission_id is same as student_id_number
        DB::table('admission_students')->update([
            'admission_id' => DB::raw('student_id_number')
        ]);

        DB::table('student_promotions')->update([
            'from_admission_id' => DB::raw('from_student_id_number'),
            'to_admission_id' => DB::raw('to_student_id_number')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admission_students', function (Blueprint $table) {
            $table->dropColumn('admission_id');
        });

        Schema::table('student_promotions', function (Blueprint $table) {
            $table->dropColumn(['from_admission_id', 'to_admission_id']);
        });
    }
};
