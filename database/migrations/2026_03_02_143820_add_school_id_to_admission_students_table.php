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
        Schema::table('admission_students', function (Blueprint $table) {

            $table->unsignedBigInteger('school_id')->after('id');

            $table->index('school_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admission_students', function (Blueprint $table) {

            $table->dropIndex(['school_id']);
            $table->dropColumn('school_id');

        });
    }
};
