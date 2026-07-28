<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admission_students', function (Blueprint $table) {
            $table->date('dob')->nullable()->after('mother_name');
            $table->string('nid_birth_certificate')->nullable()->after('dob');
            $table->string('blood_group')->nullable()->after('nid_birth_certificate');
        });
    }

    public function down(): void
    {
        Schema::table('admission_students', function (Blueprint $table) {
            $table->dropColumn(['dob', 'nid_birth_certificate', 'blood_group']);
        });
    }
};
