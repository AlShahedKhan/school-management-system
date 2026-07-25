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
        Schema::table('school_subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('school_subjects', 'fail_mark')) {
                $table->integer('fail_mark')->nullable()->after('marks');
            }
        });
    }

    public function down(): void
    {
        Schema::table('school_subjects', function (Blueprint $table) {
            if (Schema::hasColumn('school_subjects', 'fail_mark')) {
                $table->dropColumn('fail_mark');
            }
        });
    }
};
