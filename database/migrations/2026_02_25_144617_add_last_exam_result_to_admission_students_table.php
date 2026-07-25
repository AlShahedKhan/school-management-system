<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('admission_students', function (Blueprint $table) {
            $table->string('last_exam_result')->nullable()->after('previous_session');
        });
    }

    public function down(): void
    {
        Schema::table('admission_students', function (Blueprint $table) {
            $table->dropColumn('last_exam_result');
        });
    }
};
